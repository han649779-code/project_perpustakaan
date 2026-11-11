<?php
// Halaman Login
require_once __DIR__ . '/config/helper.php';
mulai_session();

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$username_cookie = $_COOKIE['ingat_username'] ?? '';
$judul_halaman = 'Masuk';
$tanpa_navbar = true;
include __DIR__ . '/includes/header.php';
?>
<div class="container-fluid sw-login">
    <div class="row min-vh-100">
        <div class="col-lg-6 sw-login-visual">
            <div class="fw-bold fs-5"><i class="bi bi-journal-bookmark-fill" style="color:var(--stabilo)"></i> Selfwise Library</div>
            <div class="my-5">
                <h1>Kelola koleksi buku dengan rapi.</h1>
                <p class="mt-3">Catat judul, atur stok, dan simpan sampul buku dalam satu tempat.</p>
            </div>
            <div class="sw-rak" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <div class="col-lg-6 sw-login-form">
            <div class="inner">
                <h2 class="mb-1">Masuk</h2>
                <p class="text-secondary mb-4">Gunakan akun pustakawan untuk melanjutkan.</p>

                <form action="proses_login.php" method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               autocomplete="username" value="<?= e($username_cookie) ?>" <?= $username_cookie ? '' : 'autofocus' ?>>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required
                               autocomplete="current-password" <?= $username_cookie ? 'autofocus' : '' ?>>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"
                               <?= $username_cookie ? 'checked' : '' ?>>
                        <label class="form-check-label" for="remember">Ingat username saya</label>
                    </div>
                    <button type="submit" class="btn btn-sw w-100 py-2">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
