<?php

namespace App\Database;

abstract class BaseRepository
{
    protected \PDO $connection;
    protected string $table;

    public function __construct()
    {
        $this->connection = DatabaseConnection::getInstance()->getConnection();
    }

    protected function executeQuery(string $query): \PDOStatement|bool
    {
        try {
            $result = $this->connection->query($query);
            return $result;
        } catch (\PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw new \Exception("Query Error: " . $e->getMessage());
            }
            return false;
        }
    }

    protected function fetchRow(string $query): ?array
    {
        $result = $this->executeQuery($query);
        if ($result instanceof \PDOStatement) {
            $row = $result->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        }
        return null;
    }

    protected function fetchAll(string $query): array
    {
        $result = $this->executeQuery($query);
        if ($result instanceof \PDOStatement) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    protected function prepare(string $query): \PDOStatement
    {
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare Error: Unable to prepare statement");
        }
        return $stmt;
    }

    protected function executeStatement(\PDOStatement $stmt): bool
    {
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Execute Error: " . $e->getMessage());
        }
    }

    protected function getLastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    protected function getAffectedRows(\PDOStatement $stmt): int
    {
        return $stmt->rowCount();
    }

    protected function escape(string $value): string
    {
        return substr($this->connection->quote($value), 1, -1);
    }

    public function executeUpdate(string $query, string $types, ...$values): bool
    {
        $stmt = $this->prepare($query);

        foreach ($values as $index => $value) {
            $paramIndex = $index + 1;
            $stmt->bindValue($paramIndex, $value);
        }

        return $this->executeStatement($stmt);
    }

    public function findByQuery(string $query): ?array
    {
        return $this->fetchRow($query);
    }
}
