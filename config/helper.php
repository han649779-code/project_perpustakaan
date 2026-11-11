<?php
/**
 * Fungsi bantu: session, autentikasi, flash message, CSRF, status buku, upload.
 */

function mulai_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/** Halaman terproteksi: wajib login, jika tidak redirect ke index.php */
function wajib_login(): void
{
    mulai_session();
    if (empty($_SESSION['user_id'])) {
        flash('warning', 'Silakan login terlebih dahulu.');
        header('Location: index.php');
        exit;
    }
}

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function flash(string $tipe, string $pesan): void
{
    mulai_session();
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function ambil_flash(): ?array
{
    mulai_session();
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    mulai_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function verifikasi_csrf(): void
{
    mulai_session();
    $kirim = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $kirim)) {
        http_response_code(403);
        exit('Token keamanan tidak valid. Kembali dan coba lagi.');
    }
}

/** Percabangan if-else untuk label status ketersediaan buku */
function status_buku(int $stok): array
{
    if ($stok > 0) {
        return ['label' => 'Tersedia', 'kelas' => 'badge-tersedia'];
    }
    return ['label' => 'Dipinjam', 'kelas' => 'badge-dipinjam'];
}

/** Array kategori (dipakai di form tambah/edit dan filter) */
function daftar_kategori(): array
{
    return ['Teknologi', 'Sistem Informasi', 'Sains', 'Bisnis', 'Sastra', 'Sejarah', 'Pendidikan', 'Lainnya'];
}

/**
 * Validasi & simpan file sampul.
 * @return array [nama_file|null, pesan_error|null]
 */
function proses_upload_sampul(array $file): array
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null]; // tidak mengunggah apa pun
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return [null, 'Ukuran file melebihi batas 2 MB.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload gagal (kode ' . $file['error'] . '). Coba lagi.'];
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        return [null, 'Ukuran file maksimal 2 MB.'];
    }

    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)) {
        return [null, 'Format file harus .jpg atau .png.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true) || @getimagesize($file['tmp_name']) === false) {
        return [null, 'Isi file bukan gambar JPG/PNG yang valid.'];
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $nama_baru = 'sampul_' . bin2hex(random_bytes(8)) . '.' . ($mime === 'image/png' ? 'png' : 'jpg');
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $nama_baru)) {
        return [null, 'Gagal menyimpan file ke folder uploads.'];
    }
    return [$nama_baru, null];
}

/** Hapus file sampul fisik dari folder uploads */
function hapus_file_sampul(?string $nama_file): void
{
    if (!$nama_file) {
        return;
    }
    $path = UPLOAD_DIR . basename($nama_file);
    if (is_file($path)) {
        unlink($path);
    }
}

/**
 * Validasi input form buku (dipakai proses_tambah & proses_edit).
 * @return array [data_bersih, daftar_error]
 */
function validasi_buku(array $post): array
{
    $data = [
        'judul'        => trim($post['judul'] ?? ''),
        'kategori'     => $post['kategori'] ?? '',
        'penulis'      => trim($post['penulis'] ?? ''),
        'tahun_terbit' => $post['tahun_terbit'] ?? '',
        'stok'         => $post['stok'] ?? '',
    ];
    $error = [];

    if ($data['judul'] === '' || mb_strlen($data['judul']) > 200) {
        $error[] = 'Judul wajib diisi (maksimal 200 karakter).';
    }
    if (!in_array($data['kategori'], daftar_kategori(), true)) {
        $error[] = 'Pilih kategori yang tersedia.';
    }
    if ($data['penulis'] === '' || mb_strlen($data['penulis']) > 120) {
        $error[] = 'Penulis wajib diisi (maksimal 120 karakter).';
    }
    $tahun = filter_var($data['tahun_terbit'], FILTER_VALIDATE_INT);
    if ($tahun === false || $tahun < 1900 || $tahun > (int) date('Y') + 1) {
        $error[] = 'Tahun terbit harus berupa angka antara 1900 dan ' . ((int) date('Y') + 1) . '.';
    }
    $stok = filter_var($data['stok'], FILTER_VALIDATE_INT);
    if ($stok === false || $stok < 0) {
        $error[] = 'Stok harus berupa angka 0 atau lebih.';
    }

    $data['tahun_terbit'] = $tahun === false ? 0 : $tahun;
    $data['stok']         = $stok === false ? 0 : $stok;
    return [$data, $error];
}
