<?php

namespace App\Database;

class DatabaseConnection
{
    private static ?self $instance = null;
    private ?\PDO $connection = null;
    private string $driver = 'mysql';

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect(): void
    {
        $pgHost = getenv('PGHOST');

        if ($pgHost && !empty($pgHost)) {
            $this->connectPostgreSQL();
        } else {
            $this->connectMySQL();
        }
    }

    private function connectPostgreSQL(): void
    {
        $this->driver = 'pgsql';

        $host = getenv('PGHOST') ?: 'localhost';
        $port = getenv('PGPORT') ?: '5432';
        $user = getenv('PGUSER') ?: 'postgres';
        $pass = getenv('PGPASSWORD') ?: '';
        $dbname = getenv('PGDATABASE') ?: 'nanocomp_db';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            $this->connection = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("PostgreSQL Connection Error: " . $e->getMessage());
        }
    }

    private function connectMySQL(): void
    {
        $this->driver = 'mysql';

        $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
        $user = defined('DB_USERNAME') ? DB_USERNAME : (getenv('DB_USERNAME') ?: 'root');
        $pass = defined('DB_PASSWORD') ? DB_PASSWORD : (getenv('DB_PASSWORD') ?: '');
        $dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'nanocomp_db');
        $port = getenv('DB_PORT') ?: '3306';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        try {
            $this->connection = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("MySQL Connection Error: " . $e->getMessage());
        }
    }

    public function getConnection(): \PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function isPostgreSQL(): bool
    {
        return $this->driver === 'pgsql';
    }

    public function isMySQL(): bool
    {
        return $this->driver === 'mysql';
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize DatabaseConnection");
    }

    public function __destruct()
    {
        $this->connection = null;
    }
}
