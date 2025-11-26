<?php
// Ortak hata yönetimi ve log fonksiyonları
function db_log($message) {
    error_log('[DB_LOG] ' . $message, 3, __DIR__ . '/../db.log');
}

function db_error($error) {
    error_log('[DB_ERROR] ' . $error, 3, __DIR__ . '/../db_error.log');
    echo "Bir veritabanı hatası oluştu!";
}
