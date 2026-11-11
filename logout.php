<?php
// Hapus Session & Cookie lalu kembali ke halaman login
require_once __DIR__ . '/config/helper.php';
mulai_session();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
setcookie('ingat_username', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_destroy();

// Mulai session baru hanya untuk menampilkan pesan
mulai_session();
flash('success', 'Anda sudah keluar.');
header('Location: index.php');
exit;
