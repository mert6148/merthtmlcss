<?php
/**
 * Merthtmlcss Ana PHP Dosyası
 * Modern web uygulaması için ana konfigürasyon ve fonksiyonlar
 * 
 * @author Mert Doğanay
 * @version 2.1.0
 * @since 2024
 */

// Hata raporlama (sadece geliştirme ortamında)
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Güvenlik ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Zaman dilimi ayarı
date_default_timezone_set('Europe/Istanbul');

// Ana konfigürasyon değişkenleri
$config = [
    'site_name' => 'Merthtmlcss',
    'site_description' => 'Modern Web Geliştirme Platformu',
    'site_version' => '2.1.0',
    'site_url' => 'https://merthtmlcss.com',
    'admin_email' => 'info@merthtmlcss.com',
    'developer_email' => 'mertdoganay437@gmail.com',
    'timezone' => 'Europe/Istanbul',
    'charset' => 'UTF-8',
    'debug_mode' => false
];

// Ana sayfa mesajları
$anasayfaMesaj = "Merthtmlcss projesine <strong>hoş geldiniz!</strong> Modern web teknolojileriyle ilgili örnekler, açıklamalar ve kaynaklar burada sizi bekliyor.";
$iletisimMail = isset($config['admin_email']) ? sanitizeInput($config['admin_email']) : 'info@merthtmlcss.com';
$iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=' . rawurlencode('İletişim') . '&body=' . rawurlencode('Merhaba, Merthtmlcss ile ilgili bir sorum var.') . '" class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
$ekbilgi = isset($config['site_name']) ? sanitizeInput($config['site_name']) : 'Merthtmlcss';

// Güvenlik fonksiyonları
/**
 * Kullanıcıdan gelen veriyi XSS ve injection saldırılarına karşı temizler.
 * Hem string hem dizi için çalışır.
 * @param mixed $input
 * @return mixed
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    // Gereksiz boşlukları temizle, özel karakterleri HTML entity'ye çevir
    return htmlspecialchars(trim((string)$input), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Yardımcı fonksiyonlar
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF'], '.php');
}

function isCurrentPage($page) {
    return getCurrentPage() === $page;
}

function formatDate($date, $format = 'd.m.Y H:i') {
    return date($format, strtotime($date));
}

function getFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// API yanıt fonksiyonları
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function errorResponse($message, $status = 400) {
    jsonResponse(['error' => $message], $status);
}

function successResponse($data, $message = 'Başarılı') {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

// Log fonksiyonu
function logActivity($action, $details = '') {
    $logFile = 'logs/activity.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = sprintf(
        "[%s] %s: %s - IP: %s - User Agent: %s\n",
        date('Y-m-d H:i:s'),
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    );
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Gelişmiş log fonksiyonu
function writeLog($message, $level = 'INFO', $logFile = 'app.log') {
    $date = date('Y-m-d H:i:s');
    $logLine = "[$date][$level] $message" . PHP_EOL;
    $logPath = __DIR__ . '/' . $logFile;
    $fp = @fopen($logPath, 'a');
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $logLine);
        flock($fp, LOCK_UN);
        fclose($fp);
    } else {
        // Dosya açılamazsa ekrana yaz
        error_log($logLine);
    }
}

// Cache ayarları
$cacheConfig = [
    'dir' => __DIR__ . '/cache/', // Cache dizini
    'default_ttl' => 300,         // Varsayılan süre (saniye)
    'max_size' => 10 * 1024 * 1024 // Maksimum toplam boyut (10 MB)
];

// Basit dosya tabanlı cache fonksiyonları (güncellenmiş)
function setCache($key, $data, $ttl = null) {
    global $cacheConfig;
    $cacheDir = $cacheConfig['dir'];
    $ttl = $ttl ?? $cacheConfig['default_ttl'];
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $file = $cacheDir . md5($key) . '.cache';
    $expire = time() + $ttl;
    $content = serialize(['expire' => $expire, 'data' => $data]);
    file_put_contents($file, $content, LOCK_EX);
    // Maksimum boyut kontrolü
    enforceCacheSizeLimit();
}

function getCache($key) {
    global $cacheConfig;
    $file = $cacheConfig['dir'] . md5($key) . '.cache';
    if (!file_exists($file)) return false;
    $content = @file_get_contents($file);
    if ($content === false) return false;
    $cache = @unserialize($content);
    if (!$cache || !isset($cache['expire']) || time() > $cache['expire']) {
        @unlink($file);
        return false;
    }
    return $cache['data'];
}

// Cache boyutunu sınırla (en eski dosyaları sil)
function enforceCacheSizeLimit() {
    global $cacheConfig;
    $cacheDir = $cacheConfig['dir'];
    $maxSize = $cacheConfig['max_size'];
    $files = glob($cacheDir . '*.cache');
    $totalSize = 0;
    $fileTimes = [];
    foreach ($files as $file) {
        $totalSize += filesize($file);
        $fileTimes[$file] = filemtime($file);
    }
    if ($totalSize > $maxSize) {
        // En eski dosyaları sil
        asort($fileTimes);
        foreach ($fileTimes as $file => $time) {
            if ($totalSize <= $maxSize) break;
            $totalSize -= filesize($file);
            @unlink($file);
        }
    }
}

// Performans izleme
$startTime = microtime(true);

function getExecutionTime() {
    global $startTime;
    return round((microtime(true) - $startTime) * 1000, 2);
}

// Otomatik temizlik
function cleanupOldFiles($directory, $maxAge = 86400) {
    if (!is_dir($directory)) return;
    
    $files = glob($directory . '/*');
    foreach ($files as $file) {
        if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
            unlink($file);
        }
    }
}

// Haftalık temizlik (sadece bir kez çalıştır)
$cleanupFlag = "cache/last_cleanup.flag";
if (!file_exists($cleanupFlag) || (time() - filemtime($cleanupFlag)) > 604800) {
    cleanupOldFiles('cache', 86400); // 1 gün
    cleanupOldFiles('logs', 2592000); // 30 gün
    file_put_contents($cleanupFlag, time());
}

// Session başlatma
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token oluşturma
$csrfToken = generateCSRFToken();

// Sayfa bilgileri
$pageInfo = [
    'title' => $config['site_name'],
    'description' => $config['site_description'],
    'keywords' => 'web geliştirme, HTML, CSS, JavaScript, PHP, Python, API',
    'author' => 'Mert Doğanay',
    'version' => $config['site_version'],
    'last_updated' => date('Y-m-d H:i:s')
];

// Sosyal medya linkleri
$socialLinks = [
    'twitter' => 'https://x.com/MertDoganay61',
    'github' => 'https://github.com/mert6148',
    'youtube' => 'https://www.youtube.com/@mert_doganay',
    'linkedin' => '#',
    'instagram' => '#'
];

// Teknoloji listesi
$technologies = [
    'Frontend' => ['HTML5', 'CSS3', 'JavaScript ES6+', 'React', 'Vue.js'],
    'Backend' => ['PHP 8.0+', 'Python 3.8+', 'Node.js'],
    'Database' => ['MySQL', 'PostgreSQL', 'SQLite'],
    'Tools' => ['Git', 'Docker', 'VS Code', 'Postman']
];

// İstatistikler (örnek)
$stats = [
    'total_projects' => 15,
    'total_users' => 1250,
    'total_downloads' => 8900,
    'last_update' => date('Y-m-d')
];

// Çevre değişkenleri
$environment = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize')
];

// Güvenlik kontrolü
$securityChecks = [
    'https_enabled' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'secure_headers' => true,
    'csrf_protection' => true,
    'input_sanitization' => true
];

// Debug bilgileri (sadece geliştirme ortamında)
if ($config['debug_mode']) {
    $debugInfo = [
        'execution_time' => getExecutionTime() . 'ms',
        'memory_usage' => memory_get_usage(true),
        'peak_memory' => memory_get_peak_usage(true),
        'included_files' => count(get_included_files())
    ];
}

// Çıktı tamponlaması başlat
ob_start();

// Log aktivitesi
logActivity('page_load', 'Ana sayfa yüklendi - ' . getCurrentPage());

// Çıktı temizleme fonksiyonu
function cleanupOutput() {
    $output = ob_get_clean();
    
    // Gereksiz boşlukları temizle
    $output = preg_replace('/\s+/', ' ', $output);
    $output = trim($output);
    
    return $output;
}

// Shutdown fonksiyonu
register_shutdown_function(function() {
    global $startTime;
    $executionTime = getExecutionTime();
    
    if ($executionTime > 1000) { // 1 saniyeden fazla
        logActivity('slow_page', "Sayfa yavaş yüklendi: {$executionTime}ms");
    }
    
    logActivity('page_end', "Sayfa tamamlandı: {$executionTime}ms");
});

// Hata yakalama
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorMessage = "PHP Error [$errno]: $errstr in $errfile on line $errline";
    logActivity('php_error', $errorMessage);
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        throw new ErrorException($errorMessage, 0, $errno, $errfile, $errline);
    }
    
    return true;
});

// Exception yakalama
set_exception_handler(function($exception) {
    $errorMessage = "Uncaught Exception: " . $exception->getMessage();
    logActivity('exception', $errorMessage);
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo "<h1>Hata</h1>";
        echo "<p>$errorMessage</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Bir hata oluştu</h1>";
        echo "<p>Lütfen daha sonra tekrar deneyin.</p>";
    }
    
    exit(1);
});

// Çıktıyı temizle ve döndür
$cleanOutput = cleanupOutput();

// Exception yakalama, çıktı temizleme ve döngü örnekleri
/**
 * Exception yakalama, çıktı tamponu ve döngülerin birlikte kullanımı örneği
 */
function ornekDonguVeException() {
    try {
        // Çıktı tamponunu başlat
        ob_start();
        echo "<strong>For döngüsü örneği:</strong><br>";
        for ($i = 1; $i <= 5; $i++) {
            if ($i % 2 == 0) {
                echo "$i çift sayı<br>";
            } else {
                echo "$i tek sayı<br>";
            }
        }
        echo "<strong>While döngüsü örneği:</strong><br>";
        $j = 1;
        while ($j <= 3) {
            echo "While döngüsü: $j<br>";
            $j++;
        }
        // Burada örnek bir hata fırlatıyoruz
        if (rand(0, 1) === 1) {
            throw new Exception("Rastgele bir hata oluştu!");
        }
        // Tamponu al ve temizle
        $icerik = ob_get_clean();
        echo "<div style='color:green;'>Döngüler başarıyla çalıştı:</div>" . $icerik;
    } catch (Exception $e) {
        // Hata olursa tamponu temizle
        ob_end_clean();
        echo "<div style='color:red;'>Hata yakalandı: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Kullanım örneği:
// ornekDonguVeException();
?>