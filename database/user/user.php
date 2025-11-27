<?php

namespace App\Database;

use App\Database\Connection;
class UserController {
    private $connection;

    public function __construct() {
        $this->connection = Connection::getInstance()->getConnection();
    }

    public function getAllUsers() {
        $stmt = $this->connection->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
