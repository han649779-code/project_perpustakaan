<?php
/**
 * Koneksi database MySQL (mysqli) + konfigurasi dasar aplikasi.
 * Sesuaikan DB_USER / DB_PASS dengan XAMPP/Laragon milik Anda.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_perpustakaan');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2 MB
define('APP_NAME', 'Selfwise Library');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan db_perpustakaan sudah diimpor.');
}
