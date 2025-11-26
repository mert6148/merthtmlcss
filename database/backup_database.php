<?php
/**
 * Veritabanı Yedekleme Scripti
 * Merthtmlcss Projesi
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>💾 Merthtmlcss Veritabanı Yedekleme Scripti</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .download { background: #e2e3e5; padding: 10px; border-radius: 5px; margin: 10px 0; }
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
    $pdo = new PDO("mysql:host=$host;dbname=$database_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Veritabanına başarıyla bağlandı!</div>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 2: Veritabanı Yapısını Alma</h3>";
    
    // Tabloları listele
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<div class='info'>📊 Yedeklenecek tablolar:</div>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>📋 $table</li>";
    }
    echo "</ul>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 3: Yedek Dosyası Oluşturma</h3>";
    
    // Yedek dosya adı
    $backup_file = 'backup_merthtmlcss_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Yedek içeriği oluştur
    $backup_content = "-- Merthtmlcss Veritabanı Yedeği\n";
    $backup_content .= "-- Oluşturulma Tarihi: " . date('Y-m-d H:i:s') . "\n";
    $backup_content .= "-- Veritabanı: $database_name\n\n";
    
    $backup_content .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $backup_content .= "START TRANSACTION;\n";
    $backup_content .= "SET time_zone = \"+00:00\";\n\n";
    
    $backup_content .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
    $backup_content .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
    $backup_content .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
    $backup_content .= "/*!40101 SET NAMES utf8mb4 */;\n\n";
    
    $backup_content .= "-- Veritabanı: `$database_name`\n\n";
    
    // Her tablo için yapı ve veri oluştur
    foreach ($tables as $table) {
        echo "<div class='info'>📋 $table tablosu yedekleniyor...</div>";
        
        // Tablo yapısını al
        $create_table = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $backup_content .= "-- Tablo için tablo yapısı `$table`\n\n";
        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup_content .= $create_table['Create Table'] . ";\n\n";
        
        // Tablo verilerini al
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $backup_content .= "-- Tablo için döküm verisi `$table`\n\n";
            
            // Sütun adlarını al
            $columns = array_keys($rows[0]);
            $backup_content .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $row_values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $row_values[] = 'NULL';
                    } else {
                        $row_values[] = "'" . addslashes($value) . "'";
                    }
                }
                $values[] = "(" . implode(', ', $row_values) . ")";
            }
            
            $backup_content .= implode(",\n", $values) . ";\n\n";
        }
        
        $backup_content .= "-- --------------------------------------------------------\n\n";
    }
    
    $backup_content .= "COMMIT;\n\n";
    $backup_content .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
    $backup_content .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
    $backup_content .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
    $backup_content .= "-- Yedekleme tamamlandı\n";
    $backup_content .= "-- Merthtmlcss Projesi\n";
    
    echo "<div class='success'>✅ Yedek içeriği oluşturuldu!</div>";
    echo "<div class='info'>📋 Yedek dosyası adı: $backup_file</div>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 4: Yedek Dosyasını Kaydetme</h3>";
    
    // Yedek dosyasını kaydet
    if (file_put_contents($backup_file, $backup_content)) {
        echo "<div class='success'>✅ Yedek dosyası kaydedildi: $backup_file</div>";
        
        // Dosya boyutunu göster
        $file_size = filesize($backup_file);
        $file_size_formatted = formatBytes($file_size);
        echo "<div class='info'>📊 Dosya boyutu: $file_size_formatted</div>";
        
        // İndirme linki
        echo "<div class='download'>";
        echo "<a href='$backup_file' download style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>";
        echo "⬇️ Yedek Dosyasını İndir";
        echo "</a>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>❌ Yedek dosyası kaydedilemedi!</div>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 5: Yedekleme Özeti</h3>";
    
    // İstatistikler
    $total_tables = count($tables);
    $total_records = 0;
    
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $total_records += $count;
    }
    
    echo "<div class='info'>📊 Yedekleme İstatistikleri:</div>";
    echo "<ul>";
    echo "<li>📋 Toplam Tablo: $total_tables</li>";
    echo "<li>📊 Toplam Kayıt: $total_records</li>";
    echo "<li>💾 Dosya Boyutu: $file_size_formatted</li>";
    echo "<li>📅 Yedekleme Tarihi: " . date('Y-m-d H:i:s') . "</li>";
    echo "</ul>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🎉 Yedekleme Tamamlandı!</h3>";
    echo "<div class='success'>✅ Veritabanı başarıyla yedeklendi!</div>";
    echo "<div class='info'>📋 Öneriler:</div>";
    echo "<ul>";
    echo "<li>Yedek dosyasını güvenli bir yere kopyalayın</li>";
    echo "<li>Düzenli olarak yedek alın</li>";
    echo "<li>Yedek dosyalarını şifreleyin</li>";
    echo "<li>Bulut depolama kullanın</li>";
    echo "</ul>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Hata: " . $e->getMessage() . "</div>";
    echo "<div class='info'>🔧 Sorun Giderme:</div>";
    echo "<ul>";
    echo "<li>MySQL sunucusunun çalıştığından emin olun</li>";
    echo "<li>Veritabanı adının doğru olduğunu kontrol edin</li>";
    echo "<li>Kullanıcı yetkilerini kontrol edin</li>";
    echo "<li>Dosya yazma izinlerini kontrol edin</li>";
    echo "</ul>";
}

// Dosya boyutunu formatla
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

echo "<div class='step'>";
echo "<h3>📚 phpMyAdmin'den Yedek Alma</h3>";
echo "<div class='info'>phpMyAdmin üzerinden manuel yedek almak için:</div>";
echo "<ol>";
echo "<li>phpMyAdmin'e giriş yapın</li>";
echo "<li>'merthtmlcss' veritabanını seçin</li>";
echo "<li>Üst menüden 'Export' veya 'Dışa Aktar' sekmesine tıklayın</li>";
echo "<li>Format olarak 'SQL' seçin</li>";
echo "<li>'Structure' ve 'Data' seçeneklerini işaretleyin</li>";
echo "<li>'Go' veya 'Git' butonuna tıklayın</li>";
echo "<li>Dosyayı bilgisayarınıza kaydedin</li>";
echo "</ol>";
echo "</div>";
?>