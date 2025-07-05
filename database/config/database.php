<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * Veritabanı Bağlantı Konfigürasyonu
 * Merthtmlcss Projesi - Database Klasörü
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'merthtmlcss';
    private $username = 'root';
    private $password = '';
    private $conn;
    
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
            echo "Veritabanı bağlantı hatası: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    public function closeConnection() {
        $this->conn = null;
    }
    
    // Veritabanı bağlantısını test et
    public function testConnection() {
        $db = $this->getConnection();
        
        if($db) {
            return true;
        } else {
            return false;
        }
    }
    
    // Veritabanı durumunu kontrol et
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

// Veritabanı bağlantısını test et
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

// Otomatik bağlantı testi (sadece doğrudan çalıştırıldığında)
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    testDatabaseConnection();
}
?> 