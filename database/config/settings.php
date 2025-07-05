<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * Veritabanı Ayarları ve Konfigürasyon
 * Merthtmlcss Projesi - Database Config
 */

class DatabaseSettings {
    private static $settings = [
        'development' => [
            'host' => 'localhost',
            'dbname' => 'merthtmlcss',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'debug' => true,
            'log_queries' => true
        ],
        'production' => [
            'host' => 'localhost',
            'dbname' => 'merthtmlcss_prod',
            'username' => 'db_user',
            'password' => 'secure_password',
            'charset' => 'utf8',
            'debug' => false,
            'log_queries' => false
        ]
    ];
    
    private static $current_environment = 'development';
    
    public static function getEnvironment() {
        return self::$current_environment;
    }
    
    public static function setEnvironment($env) {
        if (isset(self::$settings[$env])) {
            self::$current_environment = $env;
            return true;
        }
        return false;
    }
    
    public static function getSettings($env = null) {
        $environment = $env ?: self::$current_environment;
        return self::$settings[$environment] ?? null;
    }
    
    public static function getSetting($key, $env = null) {
        $settings = self::getSettings($env);
        return $settings[$key] ?? null;
    }
    
    public static function updateSetting($key, $value, $env = null) {
        $environment = $env ?: self::$current_environment;
        if (isset(self::$settings[$environment])) {
            self::$settings[$environment][$key] = $value;
            return true;
        }
        return false;
    }
    
    public static function getAllEnvironments() {
        return array_keys(self::$settings);
    }
    
    public static function createEnvironment($name, $settings) {
        if (!isset(self::$settings[$name])) {
            self::$settings[$name] = $settings;
            return true;
        }
        return false;
    }
    
    public static function removeEnvironment($name) {
        if (isset(self::$settings[$name]) && $name !== 'development') {
            unset(self::$settings[$name]);
            return true;
        }
        return false;
    }
    
    public static function exportSettings() {
        return [
            'environments' => self::$settings,
            'current' => self::$current_environment
        ];
    }
    
    public static function importSettings($data) {
        if (isset($data['environments']) && isset($data['current'])) {
            self::$settings = $data['environments'];
            self::$current_environment = $data['current'];
            return true;
        }
        return false;
    }
}

// Kullanım örneği:
// DatabaseSettings::setEnvironment('production');
// $host = DatabaseSettings::getSetting('host');
// $all_settings = DatabaseSettings::getSettings();
?> 