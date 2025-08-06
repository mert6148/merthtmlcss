<?php
/**
 * Database Settings Management Class
 * Veritabanı ayarlarını yönetmek için modern ve güvenli sınıf
 * 
 * @author Mert
 * @version 2.0
 * @since 2025
 */

class DatabaseSettings {
    private static $settings = [];
    private static $current_environment = 'development';
    private static $cache = [];
    private static $initialized = false;
    
    /**
     * Varsayılan ayarları yükle
     */
    private static function loadDefaultSettings() {
        self::$settings = [
            'development' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'dbname' => $_ENV['DB_NAME'] ?? 'merthtmlcss',
                'username' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASS'] ?? '',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
                'port' => $_ENV['DB_PORT'] ?? 3306,
                'debug' => true,
                'log_queries' => true,
                'timeout' => 30,
                'ssl' => false
            ],
            'production' => [
                'host' => $_ENV['DB_HOST_PROD'] ?? 'localhost',
                'dbname' => $_ENV['DB_NAME_PROD'] ?? 'merthtmlcss_prod',
                'username' => $_ENV['DB_USER_PROD'] ?? 'db_user',
                'password' => $_ENV['DB_PASS_PROD'] ?? 'secure_password',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
                'port' => $_ENV['DB_PORT_PROD'] ?? 3306,
                'debug' => false,
                'log_queries' => false,
                'timeout' => 60,
                'ssl' => true
            ],
            'testing' => [
                'host' => $_ENV['DB_HOST_TEST'] ?? 'localhost',
                'dbname' => $_ENV['DB_NAME_TEST'] ?? 'merthtmlcss_test',
                'username' => $_ENV['DB_USER_TEST'] ?? 'test_user',
                'password' => $_ENV['DB_PASS_TEST'] ?? 'test_pass',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
                'port' => $_ENV['DB_PORT_TEST'] ?? 3306,
                'debug' => true,
                'log_queries' => true,
                'timeout' => 15,
                'ssl' => false
            ]
        ];
    }
    
    /**
     * Sınıfı başlat ve güvenlik kontrollerini yap
     */
    public static function initialize() {
        if (self::$initialized) {
            return true;
        }
        
        // Varsayılan ayarları yükle
        self::loadDefaultSettings();
        
        // Ortam değişkenini kontrol et
        $env = $_ENV['APP_ENV'] ?? 'development';
        if (self::setEnvironment($env)) {
            self::log("Ortam otomatik olarak ayarlandı: {$env}");
        }
        
        // Güvenlik kontrolleri
        self::validateSettings();
        
        self::$initialized = true;
        return true;
    }
    
    /**
     * Mevcut ortamı al
     */
    public static function getEnvironment() {
        self::initialize();
        return self::$current_environment;
    }
    
    /**
     * Ortamı değiştir
     */
    public static function setEnvironment($env) {
        if (isset(self::$settings[$env])) {
            self::$current_environment = $env;
            self::clearCache();
            self::log("Ortam değiştirildi: {$env}");
            return true;
        }
        
        self::log("Geçersiz ortam: {$env}", 'ERROR');
        return false;
    }
    
    /**
     * Belirli bir ortamın tüm ayarlarını al
     */
    public static function getSettings($env = null) {
        self::initialize();
        $environment = $env ?: self::$current_environment;
        
        if (isset(self::$cache["settings_{$environment}"])) {
            return self::$cache["settings_{$environment}"];
        }
        
        $settings = self::$settings[$environment] ?? null;
        if ($settings) {
            self::$cache["settings_{$environment}"] = $settings;
        }
        
        return $settings;
    }
    
    /**
     * Belirli bir ayarı al
     */
    public static function getSetting($key, $env = null) {
        self::initialize();
        $settings = self::getSettings($env);
        return $settings[$key] ?? null;
    }
    
    /**
     * Ayarı güncelle
     */
    public static function updateSetting($key, $value, $env = null) {
        self::initialize();
        $environment = $env ?: self::$current_environment;
        
        if (isset(self::$settings[$environment])) {
            // Güvenlik kontrolü
            if (in_array($key, ['password', 'username']) && !self::validateCredential($key, $value)) {
                self::log("Geçersiz kimlik bilgisi güncelleme denemesi: {$key}", 'WARNING');
                return false;
            }
            
            self::$settings[$environment][$key] = $value;
            self::clearCache();
            self::log("Ayar güncellendi: {$environment}.{$key}");
            return true;
        }
        
        return false;
    }
    
    /**
     * Tüm ortamları listele
     */
    public static function getAllEnvironments() {
        return array_keys(self::$settings);
    }
    
    /**
     * Yeni ortam oluştur
     */
    public static function createEnvironment($name, $settings) {
        if (!isset(self::$settings[$name])) {
            // Gerekli alanları kontrol et
            $required = ['host', 'dbname', 'username', 'password'];
            foreach ($required as $field) {
                if (!isset($settings[$field])) {
                    self::log("Gerekli alan eksik: {$field}", 'ERROR');
                    return false;
                }
            }
            
            self::$settings[$name] = array_merge([
                'charset' => 'utf8mb4',
                'port' => 3306,
                'debug' => false,
                'log_queries' => false,
                'timeout' => 30,
                'ssl' => false
            ], $settings);
            
            self::clearCache();
            self::log("Yeni ortam oluşturuldu: {$name}");
            return true;
        }
        
        self::log("Ortam zaten mevcut: {$name}", 'WARNING');
        return false;
    }
    
    /**
     * Ortamı sil
     */
    public static function removeEnvironment($name) {
        if (isset(self::$settings[$name]) && $name !== 'development') {
            unset(self::$settings[$name]);
            self::clearCache();
            self::log("Ortam silindi: {$name}");
            return true;
        }
        
        self::log("Ortam silinemedi: {$name}", 'WARNING');
        return false;
    }
    
    /**
     * Ayarları dışa aktar
     */
    public static function exportSettings() {
        self::initialize();
        return [
            'environments' => self::$settings,
            'current' => self::$current_environment,
            'exported_at' => date('Y-m-d H:i:s'),
            'version' => '2.0'
        ];
    }
    
    /**
     * Ayarları içe aktar
     */
    public static function importSettings($data) {
        if (isset($data['environments']) && isset($data['current'])) {
            // Güvenlik kontrolü
            if (!self::validateImportData($data)) {
                self::log("Geçersiz import verisi", 'ERROR');
                return false;
            }
            
            self::$settings = $data['environments'];
            self::$current_environment = $data['current'];
            self::clearCache();
            self::log("Ayarlar içe aktarıldı");
            return true;
        }
        
        return false;
    }
    
    /**
     * Bağlantı DSN'sini oluştur
     */
    public static function getDSN($env = null) {
        $settings = self::getSettings($env);
        if (!$settings) {
            return null;
        }
        
        $dsn = "mysql:host={$settings['host']};port={$settings['port']};dbname={$settings['dbname']};charset={$settings['charset']}";
        
        if ($settings['ssl']) {
            $dsn .= ";sslmode=require";
        }
        
        return $dsn;
    }
    
    /**
     * PDO seçeneklerini al
     */
    public static function getPDOOptions($env = null) {
        $settings = self::getSettings($env);
        if (!$settings) {
            return [];
        }
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => $settings['timeout'],
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$settings['charset']}"
        ];
        
        if ($settings['ssl']) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $_ENV['DB_SSL_CA'] ?? null;
            $options[PDO::MYSQL_ATTR_SSL_CERT] = $_ENV['DB_SSL_CERT'] ?? null;
            $options[PDO::MYSQL_ATTR_SSL_KEY] = $_ENV['DB_SSL_KEY'] ?? null;
        }
        
        return $options;
    }
    
    /**
     * Bağlantıyı test et
     */
    public static function testConnection($env = null) {
        try {
            $dsn = self::getDSN($env);
            $options = self::getPDOOptions($env);
            $settings = self::getSettings($env);
            
            if (!$dsn || !$settings) {
                return ['success' => false, 'error' => 'Geçersiz ayarlar'];
            }
            
            $pdo = new PDO($dsn, $settings['username'], $settings['password'], $options);
            $pdo->query('SELECT 1');
            
            self::log("Bağlantı testi başarılı: " . ($env ?: self::$current_environment));
            return ['success' => true, 'message' => 'Bağlantı başarılı'];
            
        } catch (PDOException $e) {
            self::log("Bağlantı hatası: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Cache'i temizle
     */
    private static function clearCache() {
        self::$cache = [];
    }
    
    /**
     * Ayarları doğrula
     */
    private static function validateSettings() {
        foreach (self::$settings as $env => $settings) {
            $required = ['host', 'dbname', 'username', 'password'];
            foreach ($required as $field) {
                if (!isset($settings[$field])) {
                    self::log("Eksik gerekli alan: {$env}.{$field}", 'ERROR');
                }
            }
        }
    }
    
    /**
     * Kimlik bilgilerini doğrula
     */
    private static function validateCredential($key, $value) {
        if ($key === 'password') {
            return strlen($value) >= 6;
        }
        if ($key === 'username') {
            return preg_match('/^[a-zA-Z0-9_]+$/', $value);
        }
        return true;
    }
    
    /**
     * Import verilerini doğrula
     */
    private static function validateImportData($data) {
        if (!is_array($data['environments'])) {
            return false;
        }
        
        foreach ($data['environments'] as $env => $settings) {
            if (!is_array($settings)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Log mesajı yaz
     */
    private static function log($message, $level = 'INFO') {
        if (self::getSetting('debug')) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] [{$level}] DatabaseSettings: {$message}" . PHP_EOL;
            
            // Log dosyasına yaz veya error_log kullan
            error_log($logMessage, 3, __DIR__ . '/../logs/database.log');
        }
    }
}

// Otomatik başlatma
DatabaseSettings::initialize();

// Kullanım örnekleri:
/*
// Ortam değiştir
DatabaseSettings::setEnvironment('production');

// Ayar al
$host = DatabaseSettings::getSetting('host');
$all_settings = DatabaseSettings::getSettings();

// Bağlantı testi
$test = DatabaseSettings::testConnection();

// DSN al
$dsn = DatabaseSettings::getDSN();
$options = DatabaseSettings::getPDOOptions();

// PDO bağlantısı
$pdo = new PDO($dsn, $username, $password, $options);
*/ 