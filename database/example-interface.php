<?php
// Modern arayüz, CSS ve JS entegre örnek
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/includes/query_builder.php';

function my_echo($str) {
    global $output_arr;
    $output_arr[] = $str;
}

$output_arr = [];

my_echo('<!DOCTYPE html>');
my_echo('<html lang="tr">');
my_echo('<head>');
my_echo('<meta charset="UTF-8">');
my_echo('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
my_echo('<title>Kullanıcı Listesi - Merthtmlcss Database</title>');
my_echo('<link rel="stylesheet" href="style-db.css">');
my_echo('<script src="script-db.js"></script>');
my_echo('</head>');
my_echo('<body>');
my_echo('<h1>👥 Kullanıcı Listesi</h1>');

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    $query = new QueryBuilder($pdo);
    $users = $query->table('users')->select(['id','name','email'])->orderBy('id','ASC')->get();
    
    if ($users && count($users) > 0) {
        my_echo('<table>');
        my_echo('<thead><tr><th>ID</th><th>Ad Soyad</th><th>E-posta</th></tr></thead>');
        my_echo('<tbody>');
        foreach ($users as $user) {
            my_echo('<tr>');
            my_echo('<td>' . htmlspecialchars($user['id']) . '</td>');
            my_echo('<td>' . htmlspecialchars($user['name']) . '</td>');
            my_echo('<td>' . htmlspecialchars($user['email']) . '</td>');
            my_echo('</tr>');
        }
        my_echo('</tbody></table>');
        my_echo('<div class="success">Toplam ' . count($users) . ' kullanıcı listelendi.</div>');
    } else {
        my_echo('<div class="warning">Hiç kullanıcı bulunamadı.</div>');
    }
} catch (Exception $e) {
    my_echo('<div class="error">Hata: ' . htmlspecialchars($e->getMessage()) . '</div>');
}

my_echo('<button onclick="location.reload()">Yenile</button>');
my_echo('</body></html>');

// Sırayla ekrana bas
for ($i = 0; $i < count($output_arr); $i++) {
    echo $output_arr[$i];
} 