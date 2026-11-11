<?php
// Query UPDATE + penggantian file sampul
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

// Ambil data lama (untuk mengetahui sampul lama)
$stmt = $koneksi->prepare('SELECT sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lama) {
    flash('danger', 'Buku tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

[$data, $error] = validasi_buku($_POST);
[$sampul_baru, $error_upload] = proses_upload_sampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
if ($error_upload) {
    $error[] = $error_upload;
}

if ($error) {
    hapus_file_sampul($sampul_baru);
    $_SESSION['old'] = $_POST;
    flash('danger', implode(' ', $error));
    header('Location: edit_buku.php?id=' . $id);
    exit;
}

// Jika tidak ada file baru, pertahankan sampul lama
$sampul_simpan = $sampul_baru ?? $lama['sampul'];

try {
    $stmt = $koneksi->prepare('UPDATE buku SET judul = ?, kategori = ?, penulis = ?, tahun_terbit = ?, stok = ?, sampul = ? WHERE id = ?');
    $stmt->bind_param('sssiisi', $data['judul'], $data['kategori'], $data['penulis'], $data['tahun_terbit'], $data['stok'], $sampul_simpan, $id);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    hapus_file_sampul($sampul_baru);
    flash('danger', 'Perubahan gagal disimpan. Coba lagi.');
    header('Location: edit_buku.php?id=' . $id);
    exit;
}

// Database berhasil diperbarui, baru hapus file sampul lama
if ($sampul_baru !== null) {
    hapus_file_sampul($lama['sampul']);
}

flash('success', 'Data buku berhasil diperbarui.');
header('Location: dashboard.php');
exit;
