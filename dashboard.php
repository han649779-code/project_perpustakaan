<?php
// Dashboard: tampil data buku (Read) + akses menu
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

// Statistik ringkas
$stat = $koneksi->query(
    'SELECT COUNT(*) AS total,
            COALESCE(SUM(stok > 0), 0) AS tersedia,
            COALESCE(SUM(stok = 0), 0) AS dipinjam
     FROM buku'
)->fetch_assoc();

// Pencarian & filter kategori (GET)
$cari     = trim($_GET['cari'] ?? '');
$kategori = $_GET['kategori'] ?? '';

$sql    = 'SELECT id, judul, kategori, penulis, tahun_terbit, stok, sampul FROM buku WHERE 1=1';
$tipe   = '';
$param  = [];
if ($cari !== '') {
    $sql    .= ' AND (judul LIKE ? OR penulis LIKE ?)';
    $like    = '%' . $cari . '%';
    $tipe   .= 'ss';
    array_push($param, $like, $like);
}
if ($kategori !== '' && in_array($kategori, daftar_kategori(), true)) {
    $sql   .= ' AND kategori = ?';
    $tipe  .= 's';
    $param[] = $kategori;
}
$sql .= ' ORDER BY id DESC';

$stmt = $koneksi->prepare($sql);
if ($param) {
    $stmt->bind_param($tipe, ...$param);
}
$stmt->execute();
$daftar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$judul_halaman = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0">Koleksi buku</h1>
            <span class="text-secondary">Kelola data buku perpustakaan.</span>
        </div>
        <a href="tambah_buku.php" class="btn btn-sw"><i class="bi bi-plus-lg"></i> Tambah buku</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4"><div class="sw-panel sw-stat"><div class="angka"><?= (int) $stat['total'] ?></div><div class="label">Judul terdaftar</div></div></div>
        <div class="col-6 col-md-4"><div class="sw-panel sw-stat"><div class="angka" style="color:var(--daun)"><?= (int) $stat['tersedia'] ?></div><div class="label">Tersedia</div></div></div>
        <div class="col-12 col-md-4"><div class="sw-panel sw-stat"><div class="angka" style="color:#a86f00"><?= (int) $stat['dipinjam'] ?></div><div class="label">Dipinjam</div></div></div>
    </div>

    <div class="sw-panel p-3">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="search" name="cari" class="form-control" placeholder="Cari judul atau penulis" value="<?= e($cari) ?>" aria-label="Cari buku">
            </div>
            <div class="col-md-4">
                <select name="kategori" class="form-select" aria-label="Filter kategori">
                    <option value="">Semua kategori</option>
                    <?php foreach (daftar_kategori() as $kat): ?>
                        <option value="<?= e($kat) ?>" <?= $kategori === $kat ? 'selected' : '' ?>><?= e($kat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-outline-secondary" type="submit">Terapkan</button></div>
        </form>

        <div class="table-responsive">
            <table class="table sw-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th><th>Sampul</th><th>Judul</th><th>Kategori</th><th>Penulis</th>
                        <th>Tahun</th><th>Stok</th><th>Status</th><th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="9" class="text-center text-secondary py-5">
                        <?= ($cari !== '' || $kategori !== '') ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada buku. Tambahkan buku pertama Anda.' ?>
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $i => $b): $st = status_buku((int) $b['stok']); ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if ($b['sampul']): ?>
                                <img class="sw-thumb" src="uploads/<?= e($b['sampul']) ?>" alt="Sampul <?= e($b['judul']) ?>">
                            <?php else: ?>
                                <div class="sw-thumb-kosong"><i class="bi bi-book"></i></div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= e($b['judul']) ?></td>
                        <td><?= e($b['kategori']) ?></td>
                        <td><?= e($b['penulis']) ?></td>
                        <td><?= e((string) $b['tahun_terbit']) ?></td>
                        <td><?= (int) $b['stok'] ?></td>
                        <td><span class="sw-badge <?= $st['kelas'] ?>"><?= $st['label'] ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="edit_buku.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                    data-id="<?= (int) $b['id'] ?>" data-judul="<?= e($b['judul']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalHapus">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModalHapus" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="hapus_buku.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="hapusId">
            <div class="modal-header">
                <h2 class="modal-title h5" id="judulModalHapus">Hapus buku</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Buku <strong id="hapusJudul"></strong> dan file sampulnya akan dihapus permanen.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus buku</button>
            </div>
        </form>
    </div>
</div>

<?php
$skrip_tambahan = <<<'JS'
<script>
document.querySelectorAll('.btn-hapus').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
        document.getElementById('hapusId').value = tombol.dataset.id;
        document.getElementById('hapusJudul').textContent = tombol.dataset.judul;
    });
});
</script>
JS;
include __DIR__ . '/includes/footer.php';
