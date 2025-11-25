<?php

class Database
{
    private string $host = 'localhost';
    private string $dbName = 'teste_contatos';
    private string $username = 'root';
    private string $password = '123987';
    private ?\PDO $conn = null;

    public function getConnection(): \PDO
    {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        }

        return $this->conn;
    }
}
