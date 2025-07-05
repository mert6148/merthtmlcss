<?php
/**
 * Veritabanı Durum Kontrol Scripti
 * Merthtmlcss Projesi - Database Klasörü
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Merthtmlcss Veritabanı Durum Kontrolü</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .table th { background-color: #f2f2f2; }
    .status-ok { color: green; }
    .status-error { color: red; }
    .status-warning { color: orange; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
</style>";
echo '<link rel="stylesheet" href="style-db.css">';
echo '<script src="script-db.js"></script>';

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$username = 'root';
$password = '';
$database_name = 'merthtmlcss';

$checks = [];

try {
    echo "<div class='step'>";
    echo "<h3>📋 Adım 1: MySQL Sunucu Bağlantısı</h3>";
    
    // MySQL sunucu bağlantısı
    $pdo_server = new PDO("mysql:host=$host", $username, $password);
    $pdo_server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $checks['mysql_connection'] = true;
    echo "<div class='success'>✅ MySQL sunucusuna başarıyla bağlandı!</div>";
    
    // MySQL versiyonu
    $mysql_version = $pdo_server->query('SELECT VERSION()')->fetchColumn();
    echo "<div class='info'>📊 MySQL Versiyonu: $mysql_version</div>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 2: Veritabanı Varlığı</h3>";
    
    // Veritabanının varlığını kontrol et
    $databases = $pdo_server->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array($database_name, $databases)) {
        $checks['database_exists'] = true;
        echo "<div class='success'>✅ '$database_name' veritabanı mevcut!</div>";
    } else {
        $checks['database_exists'] = false;
        echo "<div class='error'>❌ '$database_name' veritabanı bulunamadı!</div>";
        echo "<div class='info'>💡 Çözüm: install.php scriptini çalıştırın.</div>";
    }
    
    echo "</div>";
    
    if ($checks['database_exists']) {
        echo "<div class='step'>";
        echo "<h3>📋 Adım 3: Veritabanı Bağlantısı</h3>";
        
        // Veritabanına bağlan
        $pdo = new PDO("mysql:host=$host;dbname=$database_name", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $checks['database_connection'] = true;
        echo "<div class='success'>✅ Veritabanına başarıyla bağlandı!</div>";
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Adım 4: Tablo Yapısı Kontrolü</h3>";
        
        // Gerekli tablolar
        $required_tables = [
            'users', 'posts', 'categories', 'comments', 'contact_messages',
            'site_settings', 'api_logs', 'media', 'password_resets',
            'post_categories', 'roles', 'user_roles', 'sessions', 'notifications'
        ];
        
        // Mevcut tabloları al
        $existing_tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<table class='table'>";
        echo "<tr><th>Tablo Adı</th><th>Durum</th><th>Kayıt Sayısı</th></tr>";
        
        foreach ($required_tables as $table) {
            if (in_array($table, $existing_tables)) {
                $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                echo "<tr>";
                echo "<td>$table</td>";
                echo "<td class='status-ok'>✅ Mevcut</td>";
                echo "<td>$count</td>";
                echo "</tr>";
                $checks["table_$table"] = true;
            } else {
                echo "<tr>";
                echo "<td>$table</td>";
                echo "<td class='status-error'>❌ Eksik</td>";
                echo "<td>-</td>";
                echo "</tr>";
                $checks["table_$table"] = false;
            }
        }
        echo "</table>";
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Adım 5: Veri Bütünlüğü Kontrolü</h3>";
        
        // Veri bütünlüğü kontrolleri
        $integrity_checks = [];
        
        // Kullanıcı kontrolü
        $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($user_count > 0) {
            $integrity_checks['users'] = true;
            echo "<div class='success'>✅ Kullanıcılar mevcut ($user_count kayıt)</div>";
        } else {
            $integrity_checks['users'] = false;
            echo "<div class='warning'>⚠️ Kullanıcı tablosu boş</div>";
        }
        
        // Yazı kontrolü
        $post_count = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
        if ($post_count > 0) {
            $integrity_checks['posts'] = true;
            echo "<div class='success'>✅ Yazılar mevcut ($post_count kayıt)</div>";
        } else {
            $integrity_checks['posts'] = false;
            echo "<div class='warning'>⚠️ Yazı tablosu boş</div>";
        }
        
        // Kategori kontrolü
        $category_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($category_count > 0) {
            $integrity_checks['categories'] = true;
            echo "<div class='success'>✅ Kategoriler mevcut ($category_count kayıt)</div>";
        } else {
            $integrity_checks['categories'] = false;
            echo "<div class='warning'>⚠️ Kategori tablosu boş</div>";
        }
        
        // Site ayarları kontrolü
        $settings_count = $pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
        if ($settings_count > 0) {
            $integrity_checks['settings'] = true;
            echo "<div class='success'>✅ Site ayarları mevcut ($settings_count kayıt)</div>";
        } else {
            $integrity_checks['settings'] = false;
            echo "<div class='warning'>⚠️ Site ayarları eksik</div>";
        }
        
        $checks['data_integrity'] = $integrity_checks;
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Adım 6: Performans Kontrolü</h3>";
        
        // Performans kontrolleri
        $performance_checks = [];
        
        // Tablo boyutları
        $table_sizes = [];
        foreach ($existing_tables as $table) {
            $size_query = "SELECT 
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size_MB'
                FROM information_schema.tables 
                WHERE table_schema = '$database_name' 
                AND table_name = '$table'";
            $size = $pdo->query($size_query)->fetchColumn();
            $table_sizes[$table] = $size;
        }
        
        echo "<div class='info'>📊 Tablo Boyutları:</div>";
        echo "<table class='table'>";
        echo "<tr><th>Tablo</th><th>Boyut (MB)</th></tr>";
        foreach ($table_sizes as $table => $size) {
            echo "<tr><td>$table</td><td>$size MB</td></tr>";
        }
        echo "</table>";
        
        // Toplam veritabanı boyutu
        $total_size = array_sum($table_sizes);
        echo "<div class='info'>📊 Toplam Veritabanı Boyutu: $total_size MB</div>";
        
        $performance_checks['total_size'] = $total_size;
        $checks['performance'] = $performance_checks;
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Adım 7: Güvenlik Kontrolü</h3>";
        
        // Güvenlik kontrolleri
        $security_checks = [];
        
        // Şifre hash kontrolü
        $password_check = $pdo->query("SELECT password FROM users LIMIT 1")->fetchColumn();
        if ($password_check && strpos($password_check, '$2y$') === 0) {
            $security_checks['password_hash'] = true;
            echo "<div class='success'>✅ Şifreler güvenli şekilde hash'lenmiş</div>";
        } else {
            $security_checks['password_hash'] = false;
            echo "<div class='error'>❌ Şifreler güvenli değil!</div>";
        }
        
        // Kullanıcı yetkileri kontrolü
        $admin_count = $pdo->query("SELECT COUNT(*) FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE r.name = 'admin'")->fetchColumn();
        if ($admin_count > 0) {
            $security_checks['admin_exists'] = true;
            echo "<div class='success'>✅ Admin kullanıcısı mevcut</div>";
        } else {
            $security_checks['admin_exists'] = false;
            echo "<div class='warning'>⚠️ Admin kullanıcısı bulunamadı</div>";
        }
        
        $checks['security'] = $security_checks;
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Adım 8: Database Manager Testi</h3>";
        
        // Database Manager testi
        if (file_exists('includes/db_manager.php')) {
            require_once 'includes/db_manager.php';
            $db_manager = new DatabaseManager();
            
            try {
                $stats = $db_manager->getStats();
                echo "<div class='success'>✅ Database Manager çalışıyor!</div>";
                echo "<div class='info'>📊 Sistem İstatistikleri:</div>";
                echo "<ul>";
                echo "<li>👥 Toplam Kullanıcı: " . $stats['total_users'] . "</li>";
                echo "<li>📝 Yayınlanan Yazı: " . $stats['total_posts'] . "</li>";
                echo "<li>💬 Toplam Yorum: " . $stats['total_comments'] . "</li>";
                echo "<li>📧 Okunmamış Mesaj: " . $stats['unread_messages'] . "</li>";
                echo "</ul>";
                
                $checks['db_manager'] = true;
            } catch (Exception $e) {
                echo "<div class='error'>❌ Database Manager hatası: " . $e->getMessage() . "</div>";
                $checks['db_manager'] = false;
            }
        } else {
            echo "<div class='error'>❌ Database Manager dosyası bulunamadı!</div>";
            $checks['db_manager'] = false;
        }
        
        echo "</div>";
    }
    
    echo "<div class='step'>";
    echo "<h3>📋 Adım 9: Genel Durum Raporu</h3>";
    
    // Genel durum değerlendirmesi
    $total_checks = count($checks);
    $passed_checks = count(array_filter($checks, function($value) {
        return $value === true || (is_array($value) && count(array_filter($value)) > 0);
    }));
    
    $success_rate = ($passed_checks / $total_checks) * 100;
    
    echo "<div class='info'>📊 Kontrol Sonuçları:</div>";
    echo "<ul>";
    echo "<li>✅ Başarılı Kontroller: $passed_checks</li>";
    echo "<li>📋 Toplam Kontrol: $total_checks</li>";
    echo "<li>📊 Başarı Oranı: %" . round($success_rate, 2) . "</li>";
    echo "</ul>";
    
    if ($success_rate >= 90) {
        echo "<div class='success'>🎉 Veritabanı durumu mükemmel!</div>";
    } elseif ($success_rate >= 70) {
        echo "<div class='warning'>⚠️ Veritabanı durumu iyi, bazı iyileştirmeler gerekli.</div>";
    } else {
        echo "<div class='error'>❌ Veritabanı durumu kritik, acil müdahale gerekli!</div>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🔧 Öneriler</h3>";
    
    if (!$checks['database_exists']) {
        echo "<div class='error'>❌ Veritabanı kurulumu gerekli!</div>";
        echo "<ul>";
        echo "<li>install.php scriptini çalıştırın</li>";
        echo "<li>phpMyAdmin üzerinden manuel kurulum yapın</li>";
        echo "</ul>";
    }
    
    if (isset($checks['data_integrity']) && !$checks['data_integrity']['users']) {
        echo "<div class='warning'>⚠️ Örnek veriler eklenmeli!</div>";
        echo "<ul>";
        echo "<li>merthtmlcss.sql dosyasını tekrar import edin</li>";
        echo "<li>Varsayılan kullanıcıları ekleyin</li>";
        echo "</ul>";
    }
    
    if (isset($checks['security']) && !$checks['security']['password_hash']) {
        echo "<div class='error'>❌ Güvenlik güncellemesi gerekli!</div>";
        echo "<ul>";
        echo "<li>Kullanıcı şifrelerini güncelleyin</li>";
        echo "<li>Güvenli hash algoritması kullanın</li>";
        echo "</ul>";
    }
    
    echo "<div class='info'>📚 Genel Öneriler:</div>";
    echo "<ul>";
    echo "<li>Düzenli yedek alın (backup.php)</li>";
    echo "<li>Güvenlik güncellemelerini takip edin</li>";
    echo "<li>Performans izleme yapın</li>";
    echo "<li>Log dosyalarını kontrol edin</li>";
    echo "</ul>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🔧 Programatik Kontrol</h3>";
    echo "<div class='code'>";
    echo "// PHP ile programatik kontrol\n";
    echo "require_once 'config/database.php';\n";
    echo "require_once 'includes/db_manager.php';\n\n";
    
    echo "\$database = new Database();\n";
    echo "\$pdo = \$database->getConnection();\n\n";
    
    echo "if (\$pdo) {\n";
    echo "    echo 'Veritabanı bağlantısı başarılı!';\n";
    echo "    \n";
    echo "    // Tabloları kontrol et\n";
    echo "    \$tables = \$pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);\n";
    echo "    echo 'Toplam tablo sayısı: ' . count(\$tables);\n";
    echo "} else {\n";
    echo "    echo 'Veritabanı bağlantı hatası!';\n";
    echo "}\n";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Hata: " . $e->getMessage() . "</div>";
    echo "<div class='info'>🔧 Sorun Giderme:</div>";
    echo "<ul>";
    echo "<li>MySQL sunucusunun çalıştığından emin olun</li>";
    echo "<li>Bağlantı bilgilerini kontrol edin</li>";
    echo "<li>Kullanıcı yetkilerini kontrol edin</li>";
    echo "<li>XAMPP/WAMP servislerini yeniden başlatın</li>";
    echo "</ul>";
}
?> 