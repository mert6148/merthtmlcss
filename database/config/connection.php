<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * Veritabanı Bağlantı Yönetimi
 * Merthtmlcss Projesi - Database Config
 */

class DatabaseConnection {
    private static $instance = null;
    private $connection = null;
    private $config = [
        'host' => 'localhost',
        'dbname' => 'merthtmlcss',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true
        ]
    ];
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
        } catch (PDOException $e) {
            throw new Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function isConnected() {
        return $this->connection !== null;
    }
    
    public function closeConnection() {
        $this->connection = null;
    }
    
    public function getConfig() {
        return $this->config;
    }
    
    public function updateConfig($key, $value) {
        if (isset($this->config[$key])) {
            $this->config[$key] = $value;
            return true;
        }
        return false;
    }
    
    public function testConnection() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getServerInfo() {
        if ($this->isConnected()) {
            return [
                'version' => $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION),
                'connection_status' => $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'server_info' => $this->connection->getAttribute(PDO::ATTR_SERVER_INFO)
            ];
        }
        return null;
    }
}

// Kullanım örneği:
// $db = DatabaseConnection::getInstance();
// $pdo = $db->getConnection();
?> 