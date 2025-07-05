<?php
/**
 * Veritabanı Test Scripti
 * Merthtmlcss Projesi - Database Klasörü
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Merthtmlcss Veritabanı Test Scripti</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
    .test-pass { background: #d4edda; border-left: 4px solid #28a745; }
    .test-fail { background: #f8d7da; border-left: 4px solid #dc3545; }
</style>";
echo '<link rel="stylesheet" href="style-db.css">';
echo '<script src="script-db.js"></script>';

$test_results = [];

try {
    echo "<div class='step'>";
    echo "<h3>📋 Test 1: Veritabanı Bağlantısı</h3>";
    
    // Veritabanı bağlantısını test et
    require_once 'config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<div class='test-result test-pass'>✅ Veritabanı bağlantısı başarılı!</div>";
        $test_results['connection'] = true;
    } else {
        echo "<div class='test-result test-fail'>❌ Veritabanı bağlantısı başarısız!</div>";
        $test_results['connection'] = false;
    }
    
    echo "</div>";
    
    if ($test_results['connection']) {
        echo "<div class='step'>";
        echo "<h3>📋 Test 2: Database Manager</h3>";
        
        // Database Manager'ı test et
        require_once 'includes/db_manager.php';
        $db_manager = new DatabaseManager();
        
        echo "<div class='test-result test-pass'>✅ Database Manager başarıyla yüklendi!</div>";
        $test_results['db_manager'] = true;
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Test 3: Kullanıcı İşlemleri</h3>";
        
        // Test kullanıcısı oluştur
        $test_email = 'test_' . time() . '@example.com';
        $test_name = 'Test User ' . time();
        $test_password = 'testpassword123';
        
        try {
            $result = $db_manager->createUser($test_name, $test_email, $test_password);
            if ($result) {
                echo "<div class='test-result test-pass'>✅ Test kullanıcısı oluşturuldu: $test_email</div>";
                $test_results['create_user'] = true;
                
                // Kullanıcıyı bul
                $user = $db_manager->getUserByEmail($test_email);
                if ($user) {
                    echo "<div class='test-result test-pass'>✅ Kullanıcı bulundu: " . $user['name'] . "</div>";
                    $test_results['get_user'] = true;
                    
                    // Test kullanıcısını sil
                    $pdo->exec("DELETE FROM users WHERE email = '$test_email'");
                    echo "<div class='test-result test-pass'>✅ Test kullanıcısı temizlendi</div>";
                } else {
                    echo "<div class='test-result test-fail'>❌ Kullanıcı bulunamadı!</div>";
                    $test_results['get_user'] = false;
                }
            } else {
                echo "<div class='test-result test-fail'>❌ Test kullanıcısı oluşturulamadı!</div>";
                $test_results['create_user'] = false;
            }
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>❌ Kullanıcı işlemleri hatası: " . $e->getMessage() . "</div>";
            $test_results['create_user'] = false;
        }
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Test 4: İstatistikler</h3>";
        
        try {
            $stats = $db_manager->getStats();
            if (is_array($stats)) {
                echo "<div class='test-result test-pass'>✅ İstatistikler alındı!</div>";
                $test_results['get_stats'] = true;
                
                echo "<div class='info'>📊 Sistem İstatistikleri:</div>";
                echo "<ul>";
                echo "<li>👥 Toplam Kullanıcı: " . $stats['total_users'] . "</li>";
                echo "<li>📝 Yayınlanan Yazı: " . $stats['total_posts'] . "</li>";
                echo "<li>💬 Toplam Yorum: " . $stats['total_comments'] . "</li>";
                echo "<li>📧 Okunmamış Mesaj: " . $stats['unread_messages'] . "</li>";
                echo "</ul>";
            } else {
                echo "<div class='test-result test-fail'>❌ İstatistikler alınamadı!</div>";
                $test_results['get_stats'] = false;
            }
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>❌ İstatistik işlemleri hatası: " . $e->getMessage() . "</div>";
            $test_results['get_stats'] = false;
        }
        
        echo "</div>";
        
        echo "<div class='step'>";
        echo "<h3>📋 Test 5: Site Ayarları</h3>";
        
        try {
            // Test ayarı oluştur
            $test_key = 'test_setting_' . time();
            $test_value = 'test_value_' . time();
            
            $result = $db_manager->updateSetting($test_key, $test_value);
            if ($result) {
                echo "<div class='test-result test-pass'>✅ Test ayarı oluşturuldu</div>";
                
                // Ayarı al
                $retrieved_value = $db_manager->getSetting($test_key);
                if ($retrieved_value === $test_value) {
                    echo "<div class='test-result test-pass'>✅ Test ayarı doğru alındı</div>";
                    $test_results['settings'] = true;
                } else {
                    echo "<div class='test-result test-fail'>❌ Test ayarı yanlış alındı!</div>";
                    $test_results['settings'] = false;
                }
                
                // Test ayarını sil
                $pdo->exec("DELETE FROM site_settings WHERE setting_key = '$test_key'");
                echo "<div class='test-result test-pass'>✅ Test ayarı temizlendi</div>";
            } else {
                echo "<div class='test-result test-fail'>❌ Test ayarı oluşturulamadı!</div>";
                $test_results['settings'] = false;
            }
        } catch (Exception $e) {
            echo "<div class='test-result test-fail'>❌ Site ayarları hatası: " . $e->getMessage() . "</div>";
            $test_results['settings'] = false;
        }
        
        echo "</div>";
    }
    
    echo "<div class='step'>";
    echo "<h3>📋 Test Sonuçları</h3>";
    
    $total_tests = count($test_results);
    $passed_tests = count(array_filter($test_results));
    $success_rate = ($total_tests > 0) ? ($passed_tests / $total_tests) * 100 : 0;
    
    echo "<div class='info'>📊 Test Özeti:</div>";
    echo "<ul>";
    echo "<li>✅ Başarılı Testler: $passed_tests</li>";
    echo "<li>📋 Toplam Test: $total_tests</li>";
    echo "<li>📊 Başarı Oranı: %" . round($success_rate, 2) . "</li>";
    echo "</ul>";
    
    if ($success_rate >= 90) {
        echo "<div class='success'>🎉 Tüm testler başarılı! Veritabanı sistemi mükemmel çalışıyor.</div>";
    } elseif ($success_rate >= 70) {
        echo "<div class='info'>⚠️ Çoğu test başarılı, bazı iyileştirmeler gerekli.</div>";
    } else {
        echo "<div class='error'>❌ Birçok test başarısız, acil müdahale gerekli!</div>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🔧 Kullanım Örnekleri</h3>";
    echo "<div class='info'>Test edilen fonksiyonların kullanım örnekleri:</div>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
    echo "// Veritabanı bağlantısı\n";
    echo "require_once 'config/database.php';\n";
    echo "\$database = new Database();\n";
    echo "\$pdo = \$database->getConnection();\n\n";
    
    echo "// Veritabanı yöneticisi\n";
    echo "require_once 'includes/db_manager.php';\n";
    echo "\$db_manager = new DatabaseManager();\n\n";
    
    echo "// Kullanıcı oluştur\n";
    echo "\$db_manager->createUser('John Doe', 'john@example.com', 'password');\n\n";
    
    echo "// İstatistikleri al\n";
    echo "\$stats = \$db_manager->getStats();\n";
    echo "echo 'Toplam kullanıcı: ' . \$stats['total_users'];\n";
    echo "</pre>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Genel Hata: " . $e->getMessage() . "</div>";
    echo "<div class='info'>🔧 Sorun Giderme:</div>";
    echo "<ul>";
    echo "<li>Veritabanı bağlantısını kontrol edin</li>";
    echo "<li>Dosya yollarını kontrol edin</li>";
    echo "<li>PHP hata loglarını kontrol edin</li>";
    echo "<li>Veritabanı yetkilerini kontrol edin</li>";
    echo "</ul>";
}
?> 