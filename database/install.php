<?php
/**
 * Veritabanı Kurulum Scripti
 * Merthtmlcss Projesi - Database Klasörü
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

function my_echo($str) {
    global $output_arr;
    $output_arr[] = $str;
}

$output_arr = [];

my_echo("<h1>🔧 Merthtmlcss Veritabanı Kurulum Scripti</h1>");
my_echo("<style>");
my_echo("    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }");
my_echo("    .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }");
my_echo("    .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }");
my_echo("    .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }");
my_echo("    .step { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }");
my_echo("    .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }");
my_echo("</style>");
my_echo('<link rel="stylesheet" href="style-db.css">');
my_echo('<script src="script-db.js"></script>');

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$username = 'root';
$password = '';
$database_name = 'merthtmlcss';

try {
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 1: Veritabanı Bağlantısı</h3>");
    
    // MySQL bağlantısı
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    my_echo("<div class='success'>✅ MySQL sunucusuna başarıyla bağlandı!</div>");
    
    // MySQL versiyonu
    $mysql_version = $pdo->query('SELECT VERSION()')->fetchColumn();
    my_echo("<div class='info'>📊 MySQL Versiyonu: $mysql_version</div>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 2: Veritabanı Oluşturma</h3>");
    
    // Veritabanı oluştur
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
    my_echo("<div class='success'>✅ '$database_name' veritabanı oluşturuldu!</div>");
    
    // Veritabanını seç
    $pdo->exec("USE `$database_name`");
    my_echo("<div class='success'>✅ Veritabanı seçildi!</div>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 3: SQL Dosyasını Okuma</h3>");
    
    // SQL dosyasını oku
    $sql_file = 'merthtmlcss.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQL dosyası bulunamadı: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    my_echo("<div class='success'>✅ SQL dosyası okundu!</div>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 4: Tabloları Oluşturma</h3>");
    
    // SQL komutlarını çalıştır
    $statements = explode(';', $sql_content);
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(--|\/\*|SET|START|COMMIT)/', $statement)) {
            try {
                $pdo->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                $error_count++;
                my_echo("<div class='error'>❌ Hata: " . $e->getMessage() . "</div>");
            }
        }
    }
    
    my_echo("<div class='success'>✅ $success_count SQL komutu başarıyla çalıştırıldı!</div>");
    if ($error_count > 0) {
        my_echo("<div class='error'>❌ $error_count komutta hata oluştu!</div>");
    }
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 5: Veritabanı Yapısını Kontrol Etme</h3>");
    
    // Tabloları listele
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    my_echo("<div class='info'>📊 Oluşturulan tablolar:</div>");
    my_echo("<ul>");
    foreach ($tables as $table) {
        my_echo("<li>✅ $table</li>");
    }
    my_echo("</ul>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 6: Örnek Verileri Kontrol Etme</h3>");
    
    // Örnek verileri kontrol et
    $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $post_count = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $category_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    
    my_echo("<div class='info'>📊 Veritabanı İstatistikleri:</div>");
    my_echo("<ul>");
    my_echo("<li>👥 Kullanıcılar: $user_count</li>");
    my_echo("<li>📝 Yazılar: $post_count</li>");
    my_echo("<li>📂 Kategoriler: $category_count</li>");
    my_echo("</ul>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>📋 Adım 7: Bağlantı Testi</h3>");
    
    // Veritabanı yöneticisi ile test
    if (file_exists('includes/db_manager.php')) {
        require_once 'includes/db_manager.php';
        $db_manager = new DatabaseManager();
        $stats = $db_manager->getStats();
        
        my_echo("<div class='success'>✅ Veritabanı yöneticisi test edildi!</div>");
        my_echo("<div class='info'>📊 Sistem İstatistikleri:</div>");
        my_echo("<ul>");
        my_echo("<li>👥 Toplam Kullanıcı: " . $stats['total_users'] . "</li>");
        my_echo("<li>📝 Yayınlanan Yazı: " . $stats['total_posts'] . "</li>");
        my_echo("<li>💬 Toplam Yorum: " . $stats['total_comments'] . "</li>");
        my_echo("<li>📧 Okunmamış Mesaj: " . $stats['unread_messages'] . "</li>");
        my_echo("</ul>");
    } else {
        my_echo("<div class='error'>❌ Veritabanı yöneticisi dosyası bulunamadı!</div>");
    }
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>🎉 Kurulum Tamamlandı!</h3>");
    my_echo("<div class='success'>✅ Veritabanı başarıyla kuruldu ve yapılandırıldı!</div>");
    my_echo("<div class='info'>📋 Sonraki Adımlar:</div>");
    my_echo("<ol>");
    my_echo("<li>phpMyAdmin'e giriş yapın</li>");
    my_echo("<li>'$database_name' veritabanını kontrol edin</li>");
    my_echo("<li>Web sitenizi test edin</li>");
    my_echo("<li>Gerekirse veritabanı ayarlarını güncelleyin</li>");
    my_echo("</ol>");
    
    my_echo("<div class='info'>🔐 Varsayılan Kullanıcı Bilgileri:</div>");
    my_echo("<ul>");
    my_echo("<li><strong>Admin:</strong> admin@merthtmlcss.com / password</li>");
    my_echo("<li><strong>Editor:</strong> editor@merthtmlcss.com / password</li>");
    my_echo("<li><strong>User:</strong> user@merthtmlcss.com / password</li>");
    my_echo("</ul>");
    
    my_echo("</div>");
    
    my_echo("<div class='step'>");
    my_echo("<h3>🔧 Kullanım Örnekleri</h3>");
    my_echo("<div class='code'>");
    my_echo("// Veritabanı bağlantısı\n");
    my_echo("require_once 'config/database.php';\n");
    my_echo("\$database = new Database();\n");
    my_echo("\$db = \$database->getConnection();\n\n");
    
    my_echo("// Veritabanı yöneticisi\n");
    my_echo("require_once 'includes/db_manager.php';\n");
    my_echo("\$db_manager = new DatabaseManager();\n");
    my_echo("\$users = \$db_manager->getUserByEmail('admin@merthtmlcss.com');\n");
    my_echo("</div>");
    my_echo("</div>");
    
} catch (Exception $e) {
    my_echo("<div class='error'>❌ Hata: " . $e->getMessage() . "</div>");
    my_echo("<div class='info'>🔧 Sorun Giderme:</div>");
    my_echo("<ul>");
    my_echo("<li>MySQL sunucusunun çalıştığından emin olun</li>");
    my_echo("<li>Kullanıcı adı ve şifrenin doğru olduğunu kontrol edin</li>");
    my_echo("<li>Veritabanı kullanıcısının yeterli yetkiye sahip olduğunu kontrol edin</li>");
    my_echo("<li>XAMPP/WAMP gibi bir paket kullanıyorsanız servislerin çalıştığını kontrol edin</li>");
    my_echo("</ul>");
}

// Sırayla ekrana bas
for ($i = 0; $i < count($output_arr); $i++) {
    echo $output_arr[$i];
}
?> 