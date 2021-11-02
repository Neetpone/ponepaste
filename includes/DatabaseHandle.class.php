<?php

class DatabaseHandle {
    private PDO $conn;

    public function __construct(string $conString, string $username, string $password) {
        $this->conn = new PDO($conString, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    public function prepare(string $query) : PDOStatement {
        return $this->conn->prepare($query);
    }

    public function query(string $query, array $params = null) : PDOStatement {
        if (empty($params)) {
            return $this->conn->query($query);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    public function querySelectOne(string $query, array $params = null, int $fetchMode = PDO::FETCH_ASSOC) : array|null {
        $stmt = $this->query($query, $params);

        if ($row = $stmt->fetch($fetchMode)) {
            return $row;
        }

        return null;
    }
}
