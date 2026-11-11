<?php
// Form tambah buku (Create)
require_once __DIR__ . '/config/helper.php';
wajib_login();

$data = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$judul_halaman = 'Tambah buku';
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-1">Tambah buku</h1>
    <p class="text-secondary mb-4">Isi data buku dan unggah sampulnya.</p>
    <div class="sw-panel p-4">
        <?php
        $aksi = 'proses_tambah.php';
        $tombol = 'Simpan buku';
        include __DIR__ . '/includes/form_buku.php';
        ?>
    </div>
</main>
<?php
$skrip_tambahan = '<script src="assets/form_buku.js"></script>';
include __DIR__ . '/includes/footer.php';
