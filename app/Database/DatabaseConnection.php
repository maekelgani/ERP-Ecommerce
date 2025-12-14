<?php

namespace App\Database;

class DatabaseConnection
{
    private static ?self $instance = null;
    private ?\PDO $connection = null;

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
        // Hardcode credentials untuk testing
        $host = 'localhost';
        $port = '3306';
        $user = 'root';
        $pass = ''; // Kosongkan jika tidak ada password, atau isi dengan password Anda
        $dbname = 'nanocomp_db';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        try {
            $this->connection = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Database Connection Error: " . $e->getMessage());
        }
    }

    public function getConnection(): \PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
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
