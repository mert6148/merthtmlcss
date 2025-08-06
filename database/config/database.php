<?php
// Modern PDO Bağlantı Konfigürasyonu - .env desteği ve gelişmiş hata yönetimi
// Merthtmlcss Projesi - Gelişmiş Database Sınıfı

// require_once __DIR__ . '/../../vendor/autoload.php'; // Bu satırı kaldırdım, composer gereksiz
use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'merthtmlcss';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }
    
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $exception) {
            error_log("Veritabanı bağlantı hatası: " . $exception->getMessage());
            echo "Veritabanı bağlantı hatası: " . $exception->getMessage();
        }
        return $this->conn;
    }
    
    public function closeConnection() {
        $this->conn = null;
    }
    
    public function testConnection() {
        $db = $this->getConnection();
        return $db ? true : false;
    }
    
    public function getDatabaseInfo() {
        try {
            $db = $this->getConnection();
            if ($db) {
                $version = $db->query('SELECT VERSION()')->fetchColumn();
                $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                return [
                    'connected' => true,
                    'version' => $version,
                    'tables' => $tables,
                    'table_count' => count($tables)
                ];
            }
        } catch (Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage()
            ];
        }
        return ['connected' => false];
    }
}

function testDatabaseConnection() {
    $database = new Database();
    $db = $database->getConnection();
    if($db) {
        echo "✅ Veritabanı bağlantısı başarılı!";
        return true;
    } else {
        echo "❌ Veritabanı bağlantısı başarısız!";
        return false;
    }
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    testDatabaseConnection();
} 