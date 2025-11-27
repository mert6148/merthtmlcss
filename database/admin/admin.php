<?php

use App\Database\AdminController;
use App\Database\DatabaseConnection;
require_once '../../vendor/autoload.php';
$adminController = new AdminController();
$admins = $adminController->getAllAdmins();

namespace App\Database;

class AdminController {
    private $dbConnection;

    public function __construct() {
        $this->dbConnection = new DatabaseConnection();
    }

    public function getAllAdmins() {
        $conn = $this->dbConnection->getConnection();
        $stmt = $conn->prepare("SELECT * FROM admins");
        $stmt = $conn->prepare("SELECT * FROM admins WHERE status = :status");
        $status = 'active';
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
