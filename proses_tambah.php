<?php
// Query INSERT + operasi upload file sampul
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_buku.php');
    exit;
}
verifikasi_csrf();

[$data, $error] = validasi_buku($_POST);
[$nama_sampul, $error_upload] = proses_upload_sampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
if ($error_upload) {
    $error[] = $error_upload;
}

if ($error) {
    hapus_file_sampul($nama_sampul);
    $_SESSION['old'] = $_POST;
    flash('danger', implode(' ', $error));
    header('Location: tambah_buku.php');
    exit;
}

try {
    $stmt = $koneksi->prepare('INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssiis', $data['judul'], $data['kategori'], $data['penulis'], $data['tahun_terbit'], $data['stok'], $nama_sampul);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    hapus_file_sampul($nama_sampul); // jangan tinggalkan file yatim
    flash('danger', 'Data gagal disimpan. Coba lagi.');
    header('Location: tambah_buku.php');
    exit;
}

flash('success', 'Buku “' . $data['judul'] . '” berhasil ditambahkan.');
header('Location: dashboard.php');
exit;
