<?php
/**
 * Admin Panel Controller & Classification System
 * Veritabanı yönetim operasyonları ve role-based sınıflandırma
 */

namespace App\Database;

use PDO;
use Exception;

// Constants
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('ROLE_ADMIN', 'admin');
define('ROLE_OPERATOR', 'operator');
define('ROLE_VIEWER', 'viewer');
define('PARAM_STATUS', ':status');

require_once '../../vendor/autoload.php';

/**
 * UserRole: Rol tabanlı sınıflandırma
 */
class UserRole {
    private $role;
    private $permissions = [];

    const ROLES = [
        'admin' => ['create', 'read', 'update', 'delete', 'backup', 'restore', 'manage_users'],
        'operator' => ['create', 'read', 'update', 'backup', 'view_logs'],
        'viewer' => ['read', 'view_logs']
    ];

    public function __construct($role = 'viewer') {
        $this->role = strtolower($role);
        $this->permissions = self::ROLES[$this->role] ?? self::ROLES['viewer'];
    }

    public function getRole() {
        return $this->role;
    }

    public function hasPermission($permission) {
        return in_array($permission, $this->permissions);
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isOperator() {
        return $this->role === 'operator';
    }

    public function isViewer() {
        return $this->role === 'viewer';
    }
}

/**
 * DatabaseClassifier: Veritabanı objeleri sınıflandırma
 */
class DatabaseClassifier {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    /**
     * Tablo tipini sınıflandır
     */
    public function classifyTable($tableName) {
        try {
            $stmt = $this->conn->prepare("SELECT TABLE_TYPE, TABLE_ROWS, DATA_LENGTH FROM information_schema.TABLES WHERE TABLE_NAME = :name AND TABLE_SCHEMA = DATABASE()");
            $stmt->bindParam(':name', $tableName);
            $stmt->execute();
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$info) {
                return null;
            }

            return [
                'name' => $tableName,
                'type' => $info['TABLE_TYPE'],
                'rows' => $info['TABLE_ROWS'],
                'size' => $info['DATA_LENGTH'],
                'classification' => $this->getTableClassification($info)
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Tablo sınıflandırmasını belirle
     */
    private function getTableClassification($tableInfo) {
        $rows = $tableInfo['TABLE_ROWS'];
        $classification = 'small';

        if ($rows > 1000000) {
            $classification = 'large';
        } elseif ($rows > 100000) {
            $classification = 'medium';
        } elseif ($rows > 1000) {
            $classification = 'normal';
        }

        return $classification;
    }

    /**
     * Kolon tipini sınıflandır
     */
    public function classifyColumn($tableName, $columnName) {
        try {
            $stmt = $this->conn->prepare("SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY FROM information_schema.COLUMNS WHERE TABLE_NAME = :table AND COLUMN_NAME = :column AND TABLE_SCHEMA = DATABASE()");
            $stmt->bindParam(':table', $tableName);
            $stmt->bindParam(':column', $columnName);
            $stmt->execute();
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$info) {
                return null;
            }

            return [
                'name' => $columnName,
                'type' => $info['COLUMN_TYPE'],
                'nullable' => $info['IS_NULLABLE'] === 'YES',
                'key' => $info['COLUMN_KEY'],
                'classification' => $this->getColumnClassification($info['COLUMN_TYPE'])
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Kolon type sınıflandırması
     */
    private function getColumnClassification($type) {
        $classification = 'other';

        if (strpos($type, 'int') !== false) {
            $classification = 'numeric';
        } elseif (strpos($type, 'varchar') !== false || strpos($type, 'text') !== false) {
            $classification = 'string';
        } elseif (strpos($type, 'date') !== false || strpos($type, 'time') !== false) {
            $classification = 'datetime';
        } elseif (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
            $classification = 'decimal';
        } elseif (strpos($type, 'json') !== false) {
            $classification = 'json';
        }

        return $classification;
    }

    /**
     * Tüm tabloları sınıflandır
     */
    public function classifyAllTables() {
        try {
            $stmt = $this->conn->query("SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $classified = ['large' => [], 'medium' => [], 'normal' => [], 'small' => []];

            foreach ($tables as $table) {
                $classification = $this->getTableClassification($table);
                $classified[$classification][] = [
                    'name' => $table['TABLE_NAME'],
                    'rows' => $table['TABLE_ROWS'],
                    'size' => $table['DATA_LENGTH']
                ];
            }

            return $classified;
        } catch (Exception $e) {
            return [];
        }
    }
}

/**
 * AdminController: Geliştirilmiş yönetim operasyonları
 */
class AdminController {
    private $dbConnection;
    private $conn;
    private $classifier;

    public function __construct() {
        try {
            $this->dbConnection = new DatabaseConnection();
            $this->conn = $this->dbConnection->getConnection();
            $this->classifier = new DatabaseClassifier($this->conn);
        } catch (Exception $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]));
        }
    }

    /**
     * Tüm aktif admin kullanıcılarını al
     */
    public function getAllAdmins() {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email, role, status, created_at FROM admins WHERE status = :status ORDER BY created_at DESC");
            $stmt->bindParam(':status', STATUS_ACTIVE, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => 'Admin kullanıcıları alınamadı'];
        }
    }
}

// API endpoint'leri
header('Content-Type: application/json');

$action = $_GET['action'] ?? null;
$controller = new AdminController();

if ($action === 'stats') {
    echo json_encode($controller->getDatabaseStats());
} elseif ($action === 'health') {
    echo json_encode($controller->checkDatabaseHealth());
} elseif ($action === 'admins') {
    echo json_encode($controller->getAllAdmins());
} elseif ($action === 'backup') {
    echo json_encode($controller->createBackup());
} elseif ($action === 'table-classification' && isset($_GET['table'])) {
    echo json_encode($controller->getTableClassification($_GET['table']));
} elseif ($action === 'column-classification' && isset($_GET['table']) && isset($_GET['column'])) {
    echo json_encode($controller->getColumnClassification($_GET['table'], $_GET['column']));
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}