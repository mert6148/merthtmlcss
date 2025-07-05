<?php
// Modern arayüz, CSS ve JS entegre örnek
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/includes/query_builder.php';

echo '<!DOCTYPE html>';
echo '<html lang="tr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Kullanıcı Listesi - Merthtmlcss Database</title>';
echo '<link rel="stylesheet" href="style-db.css">';
echo '<script src="script-db.js"></script>';
echo '</head>';
echo '<body>';
echo '<h1>👥 Kullanıcı Listesi</h1>';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    $query = new QueryBuilder($pdo);
    $users = $query->table('users')->select(['id','name','email'])->orderBy('id','ASC')->get();
    
    if ($users && count($users) > 0) {
        echo '<table>';
        echo '<thead><tr><th>ID</th><th>Ad Soyad</th><th>E-posta</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($user['id']) . '</td>';
            echo '<td>' . htmlspecialchars($user['name']) . '</td>';
            echo '<td>' . htmlspecialchars($user['email']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<div class="success">Toplam ' . count($users) . ' kullanıcı listelendi.</div>';
    } else {
        echo '<div class="warning">Hiç kullanıcı bulunamadı.</div>';
    }
} catch (Exception $e) {
    echo '<div class="error">Hata: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '<button onclick="location.reload()">Yenile</button>';
echo '</body></html>'; 