<?php
/**
 * Veritabanı Kurulum Scripti
 * Merthtmlcss Projesi
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Merthtmlcss Veritabanı Kurulum Scripti</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>";

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$username = 'root';
$password = '';
$database_name = 'merthtmlcss';

try {
    echo "<div class='step'>";
    echo "<h3>📋 Adım 1: Veritabanı Bağlantısı</h3>";
    
    // MySQL bağlantısı
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ MySQL sunucusuna başarıyla bağlandı!</div>";
    
    // Veritabanı oluştur
    echo "<h3>📋 Adım 2: Veritabanı Oluşturma</h3>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "<div class='success'>✅ '$database_name' veritabanı oluşturuldu!</div>";
    
    // Veritabanını seç
    $pdo->exec("USE `$database_name`");
    echo "<div class='success'>✅ Veritabanı seçildi!</div>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 3: SQL Dosyasını Okuma</h3>";
    
    // SQL dosyasını oku
    $sql_file = 'merthtmlcss.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQL dosyası bulunamadı: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    echo "<div class='success'>✅ SQL dosyası okundu!</div>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 4: Tabloları Oluşturma</h3>";
    
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
                echo "<div class='error'>❌ Hata: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    echo "<div class='success'>✅ $success_count SQL komutu başarıyla çalıştırıldı!</div>";
    if ($error_count > 0) {
        echo "<div class='error'>❌ $error_count komutta hata oluştu!</div>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 5: Veritabanı Yapısını Kontrol Etme</h3>";
    
    // Tabloları listele
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<div class='info'>📊 Oluşturulan tablolar:</div>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>✅ $table</li>";
    }
    echo "</ul>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 6: Örnek Verileri Kontrol Etme</h3>";
    
    // Örnek verileri kontrol et
    $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $post_count = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $category_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    
    echo "<div class='info'>📊 Veritabanı İstatistikleri:</div>";
    echo "<ul>";
    echo "<li>👥 Kullanıcılar: $user_count</li>";
    echo "<li>📝 Yazılar: $post_count</li>";
    echo "<li>📂 Kategoriler: $category_count</li>";
    echo "</ul>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 7: Bağlantı Testi</h3>";
    
    // Veritabanı yöneticisi ile test
    if (file_exists('includes/db_manager.php')) {
        require_once 'includes/db_manager.php';
        $db_manager = new DatabaseManager();
        $stats = $db_manager->getStats();
        
        echo "<div class='success'>✅ Veritabanı yöneticisi test edildi!</div>";
        echo "<div class='info'>📊 Sistem İstatistikleri:</div>";
        echo "<ul>";
        echo "<li>👥 Toplam Kullanıcı: " . $stats['total_users'] . "</li>";
        echo "<li>📝 Yayınlanan Yazı: " . $stats['total_posts'] . "</li>";
        echo "<li>💬 Toplam Yorum: " . $stats['total_comments'] . "</li>";
        echo "<li>📧 Okunmamış Mesaj: " . $stats['unread_messages'] . "</li>";
        echo "</ul>";
    } else {
        echo "<div class='error'>❌ Veritabanı yöneticisi dosyası bulunamadı!</div>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🎉 Kurulum Tamamlandı!</h3>";
    echo "<div class='success'>✅ Veritabanı başarıyla kuruldu ve yapılandırıldı!</div>";
    echo "<div class='info'>📋 Sonraki Adımlar:</div>";
    echo "<ol>";
    echo "<li>phpMyAdmin'e giriş yapın</li>";
    echo "<li>'$database_name' veritabanını kontrol edin</li>";
    echo "<li>Web sitenizi test edin</li>";
    echo "<li>Gerekirse veritabanı ayarlarını güncelleyin</li>";
    echo "</ol>";
    
    echo "<div class='info'>🔐 Varsayılan Kullanıcı Bilgileri:</div>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@merthtmlcss.com / password</li>";
    echo "<li><strong>Editor:</strong> editor@merthtmlcss.com / password</li>";
    echo "<li><strong>User:</strong> user@merthtmlcss.com / password</li>";
    echo "</ul>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Hata: " . $e->getMessage() . "</div>";
    echo "<div class='info'>🔧 Sorun Giderme:</div>";
    echo "<ul>";
    echo "<li>MySQL sunucusunun çalıştığından emin olun</li>";
    echo "<li>Kullanıcı adı ve şifrenin doğru olduğunu kontrol edin</li>";
    echo "<li>Veritabanı kullanıcısının yeterli yetkiye sahip olduğunu kontrol edin</li>";
    echo "<li>XAMPP/WAMP gibi bir paket kullanıyorsanız servislerin çalıştığını kontrol edin</li>";
    echo "</ul>";
}

echo "<div class='step'>";
echo "<h3>📚 phpMyAdmin'e Aktarma Talimatları</h3>";
echo "<div class='info'>Manuel olarak phpMyAdmin'e aktarmak için:</div>";
echo "<ol>";
echo "<li>phpMyAdmin'e giriş yapın</li>";
echo "<li>Sol menüden 'New' veya 'Yeni' butonuna tıklayın</li>";
echo "<li>Veritabanı adı olarak 'merthtmlcss' girin</li>";
echo "<li>Karakter seti olarak 'utf8_general_ci' seçin</li>";
echo "<li>'Create' veya 'Oluştur' butonuna tıklayın</li>";
echo "<li>Oluşturulan veritabanını seçin</li>";
echo "<li>Üst menüden 'Import' veya 'İçe Aktar' sekmesine tıklayın</li>";
echo "<li>'Choose File' ile 'merthtmlcss.sql' dosyasını seçin</li>";
echo "<li>'Go' veya 'Git' butonuna tıklayın</li>";
echo "</ol>";
echo "</div>";
?> 