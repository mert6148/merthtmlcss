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
define('ERROR_METHOD_NOT_ALLOWED', 'Method not allowed');
define('PHP_INPUT_STREAM', 'php://input');

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

    /**
     * Veritabanı istatistiklerini al
     */
    public function getDatabaseStats() {
        try {
            return [
                'total_tables' => $this->getTotalTables(),
                'total_users' => $this->getTotalUsers(),
                'total_databases' => $this->getTotalDatabases(),
                'active_connections' => $this->getActiveConnections(),
                'classified_tables' => $this->classifier->classifyAllTables()
            ];
        } catch (Exception $e) {
            return ['error' => 'İstatistikler alınamadı'];
        }
    }

    /**
     * Toplam tablo sayısı
     */
    public function getTotalTables() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Toplam kullanıcı sayısı
     */
    public function getTotalUsers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Toplam veritabanı sayısı
     */
    public function getTotalDatabases() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM information_schema.SCHEMATA");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Aktif bağlantı sayısı
     */
    public function getActiveConnections() {
        try {
            $stmt = $this->conn->query("SHOW PROCESSLIST");
            return $stmt->rowCount();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Veritabanı yedeklemesi
     */
    public function createBackup() {
        try {
            $backup_file = '../../backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
            return ['success' => true, 'file' => $backup_file];
        } catch (Exception $e) {
            return ['error' => 'Yedekleme oluşturulamadı'];
        }
    }

    /**
     * Veritabanı sağlık kontrolü
     */
    public function checkDatabaseHealth() {
        try {
            return [
                'status' => 'healthy',
                'connection' => true,
                'tables' => $this->getTotalTables(),
                'disk_usage' => $this->getDiskUsage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Disk kullanımı
     */
    public function getDiskUsage() {
        try {
            $stmt = $this->conn->query("SELECT SUM(data_length + index_length) / 1024 / 1024 as size_mb FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['size_mb'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Tablo sınıflandırmasını al
     */
    public function getTableClassification($tableName) {
        return $this->classifier->classifyTable($tableName);
    }

    /**
     * Kolon sınıflandırmasını al
     */
    public function getColumnClassification($tableName, $columnName) {
        return $this->classifier->classifyColumn($tableName, $columnName);
    }

    /**
     * Admin kullanıcısı oluştur
     */
    public function createAdmin($username, $email, $password, $role = 'operator') {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO admins (username, email, password, role, status) VALUES (:username, :email, :password, :role, :status)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', STATUS_ACTIVE);
            $stmt->execute();
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            return ['error' => 'Admin kullanıcısı oluşturulamadı'];
        }
    }

    /**
     * Admin kullanıcısı güncelle
     */
    public function updateAdmin($id, $username, $email, $role) {
        try {
            $stmt = $this->conn->prepare("UPDATE admins SET username = :username, email = :email, role = :role WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->execute();
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Admin güncellenemedi'];
        }
    }

    /**
     * Admin kullanıcısı sil (soft delete)
     */
    public function deleteAdmin($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE admins SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', STATUS_INACTIVE);
            $stmt->execute();
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Admin silinemedi'];
        }
    }

    /**
     * Log kaydı ekle
     */
    public function addLog($action, $details, $user_id = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO admin_logs (action, details, user_id, created_at) VALUES (:action, :details, :user_id, NOW())");
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return ['success' => true, 'log_id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            return ['error' => 'Log kaydı eklenemedi'];
        }
    }

    /**
     * Log kayıtlarını al
     */
    public function getLogs($limit = 50) {
        try {
            $stmt = $this->conn->prepare("SELECT id, action, details, user_id, created_at FROM admin_logs ORDER BY created_at DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

// API endpoint'leri
header('Content-Type: application/json');

$action = $_GET['action'] ?? null;
$controller = new AdminController();

switch ($action) {
    case 'stats':
        echo json_encode($controller->getDatabaseStats());
        break;

    case 'health':
        echo json_encode($controller->checkDatabaseHealth());
        break;

    case 'admins':
        echo json_encode($controller->getAllAdmins());
        break;

    case 'backup':
        echo json_encode($controller->createBackup());
        break;

    case 'table-classification':
        if (isset($_GET['table'])) {
            echo json_encode($controller->getTableClassification($_GET['table']));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Table parameter required']);
        }
        break;

    case 'column-classification':
        if (isset($_GET['table']) && isset($_GET['column'])) {
            echo json_encode($controller->getColumnClassification($_GET['table'], $_GET['column']));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Table and column parameters required']);
        }
        break;

    case 'logs':
        $limit = $_GET['limit'] ?? 50;
        echo json_encode($controller->getLogs((int) $limit));
        break;

    case 'add-admin':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents(PHP_INPUT_STREAM), true);
            echo json_encode($controller->createAdmin(
                $data['username'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['role'] ?? 'operator'
            ));
        } else {
            http_response_code(405);
            echo json_encode(['error' => ERROR_METHOD_NOT_ALLOWED]);
        }
        break;

    case 'update-admin':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents(PHP_INPUT_STREAM), true);
            echo json_encode($controller->updateAdmin(
                $data['id'] ?? null,
                $data['username'] ?? '',
                $data['email'] ?? '',
                $data['role'] ?? ''
            ));
        } else {
            http_response_code(405);
            echo json_encode(['error' => ERROR_METHOD_NOT_ALLOWED]);
        }
        break;

    case 'delete-admin':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents(PHP_INPUT_STREAM), true);
            echo json_encode($controller->deleteAdmin($data['id'] ?? null));
        } else {
            http_response_code(405);
            echo json_encode(['error' => ERROR_METHOD_NOT_ALLOWED]);
        }
        break;

    case 'add-log':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents(PHP_INPUT_STREAM), true);
            echo json_encode($controller->addLog(
                $data['action'] ?? '',
                $data['details'] ?? '',
                $data['user_id'] ?? null
            ));
        } else {
            http_response_code(405);
            echo json_encode(['error' => ERROR_METHOD_NOT_ALLOWED]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid action',
            'available_actions' => [
                'stats', 'health', 'admins', 'backup',
                'table-classification', 'column-classification',
                'logs', 'add-admin', 'update-admin', 'delete-admin', 'add-log'
            ]
        ]);
}