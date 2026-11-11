<?php
/**
 * Header bersama. Variabel opsional sebelum include:
 *   $judul_halaman (string), $tanpa_navbar (bool)
 */
require_once __DIR__ . '/../config/helper.php';
mulai_session();
$judul_halaman = $judul_halaman ?? 'Beranda';
$tanpa_navbar  = $tanpa_navbar ?? false;
$flash         = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> | Selfwise Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (!$tanpa_navbar): ?>
    <nav class="navbar navbar-expand-md sw-nav">
        <div class="container">
            <a class="navbar-brand sw-brand" href="dashboard.php">
                <i class="bi bi-journal-bookmark-fill"></i> Selfwise Library
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="sw-user d-none d-sm-inline">
                    <i class="bi bi-person-circle"></i> <?= e($_SESSION['nama_lengkap'] ?? '') ?>
                </span>
                <a href="logout.php" class="btn btn-sm btn-outline-light">Keluar</a>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['tipe']) ?> alert-dismissible fade show mb-0" role="alert">
            <?= e($flash['pesan']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
<?php endif; ?>
