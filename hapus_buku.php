<?php
// Query DELETE + unlink file gambar
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}
verifikasi_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    flash('danger', 'ID buku tidak valid.');
    header('Location: dashboard.php');
    exit;
}

$stmt = $koneksi->prepare('SELECT judul, sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    flash('danger', 'Buku tidak ditemukan atau sudah dihapus.');
    header('Location: dashboard.php');
    exit;
}

try {
    $stmt = $koneksi->prepare('DELETE FROM buku WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    flash('danger', 'Data gagal dihapus. Coba lagi.');
    header('Location: dashboard.php');
    exit;
}

hapus_file_sampul($buku['sampul']);

flash('success', 'Buku “' . $buku['judul'] . '” berhasil dihapus.');
header('Location: dashboard.php');
exit;
