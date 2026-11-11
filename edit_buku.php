<?php
// Form edit buku (Update)
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    flash('danger', 'ID buku tidak valid.');
    header('Location: dashboard.php');
    exit;
}

$stmt = $koneksi->prepare('SELECT id, judul, kategori, penulis, tahun_terbit, stok, sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    flash('danger', 'Buku tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

// Jika validasi gagal sebelumnya, tampilkan kembali isian pengguna (sampul tetap yang tersimpan)
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$data = array_merge($buku, array_intersect_key($old, array_flip(['judul', 'kategori', 'penulis', 'tahun_terbit', 'stok'])));
$id_buku = $buku['id'];

$judul_halaman = 'Edit buku';
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-1">Edit buku</h1>
    <p class="text-secondary mb-4">Ubah informasi buku atau ganti sampulnya.</p>
    <div class="sw-panel p-4">
        <?php
        $aksi = 'proses_edit.php';
        $tombol = 'Simpan perubahan';
        include __DIR__ . '/includes/form_buku.php';
        ?>
    </div>
</main>
<?php
$skrip_tambahan = '<script src="assets/form_buku.js"></script>';
include __DIR__ . '/includes/footer.php';
