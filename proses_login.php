<?php
// Verifikasi autentikasi + Session + Cookie "Remember Me"
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
mulai_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
verifikasi_csrf();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if ($username === '' || $password === '') {
    flash('danger', 'Username dan password wajib diisi.');
    header('Location: index.php');
    exit;
}

$stmt = $koneksi->prepare('SELECT id, username, password, nama_lengkap FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    flash('danger', 'Username atau password salah.');
    header('Location: index.php');
    exit;
}

// Login berhasil: ganti ID session untuk mencegah session fixation
session_regenerate_id(true);
$_SESSION['user_id']      = (int) $user['id'];
$_SESSION['username']     = $user['username'];
$_SESSION['nama_lengkap'] = $user['nama_lengkap'];

// Cookie Remember Me: hanya menyimpan username (bukan password)
$opsi_cookie = ['path' => '/', 'httponly' => true, 'samesite' => 'Lax'];
if ($remember) {
    setcookie('ingat_username', $user['username'], $opsi_cookie + ['expires' => time() + 60 * 60 * 24 * 30]);
} else {
    setcookie('ingat_username', '', $opsi_cookie + ['expires' => time() - 3600]);
}

flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '.');
header('Location: dashboard.php');
exit;
