<?php
/**
 * Form bersama untuk tambah & edit buku.
 * Variabel yang dibutuhkan: $aksi (url proses), $data (array nilai form),
 * $tombol (teks tombol), $id_buku (opsional, untuk edit)
 */
?>
<form action="<?= e($aksi) ?>" method="post" enctype="multipart/form-data" class="row g-4" novalidate>
    <?= csrf_field() ?>
    <?php if (!empty($id_buku)): ?>
        <input type="hidden" name="id" value="<?= (int) $id_buku ?>">
    <?php endif; ?>

    <div class="col-lg-8">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul buku</label>
            <input type="text" class="form-control" id="judul" name="judul" maxlength="200" required
                   value="<?= e($data['judul'] ?? '') ?>">
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach (daftar_kategori() as $kat): ?>
                        <option value="<?= e($kat) ?>" <?= ($data['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= e($kat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="penulis" class="form-label">Penulis</label>
                <input type="text" class="form-control" id="penulis" name="penulis" maxlength="120" required
                       value="<?= e($data['penulis'] ?? '') ?>">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="tahun_terbit" class="form-label">Tahun terbit</label>
                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit"
                       min="1900" max="<?= (int) date('Y') + 1 ?>" required
                       value="<?= e((string) ($data['tahun_terbit'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label for="stok" class="form-label">Stok tersedia</label>
                <input type="number" class="form-control" id="stok" name="stok" min="0" required
                       value="<?= e((string) ($data['stok'] ?? '1')) ?>">
                <div class="form-text">Stok 1 atau lebih tampil “Tersedia”, stok 0 tampil “Dipinjam”.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <label for="sampul" class="form-label">Sampul buku</label>
        <div class="sw-cover-preview mb-2" id="previewBox">
            <?php if (!empty($data['sampul'])): ?>
                <img src="uploads/<?= e($data['sampul']) ?>" alt="Sampul saat ini" id="previewImg">
            <?php else: ?>
                <img src="" alt="" id="previewImg" hidden>
                <span class="text-secondary small" id="previewKosong"><i class="bi bi-image"></i> Belum ada sampul</span>
            <?php endif; ?>
        </div>
        <input type="file" class="form-control" id="sampul" name="sampul" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        <div class="form-text">
            JPG atau PNG, maksimal 2 MB.
            <?php if (!empty($data['sampul'])): ?>Kosongkan jika tidak ingin mengganti sampul.<?php endif; ?>
        </div>
        <div class="text-danger small mt-1" id="errorFile" hidden></div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-sw"><?= e($tombol) ?></button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
