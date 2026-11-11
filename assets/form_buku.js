(function () {
    var input = document.getElementById('sampul');
    if (!input) return;
    var img = document.getElementById('previewImg');
    var kosong = document.getElementById('previewKosong');
    var error = document.getElementById('errorFile');
    var MAKS = 2 * 1024 * 1024;

    input.addEventListener('change', function () {
        error.hidden = true;
        var f = input.files[0];
        if (!f) return;
        if (['image/jpeg', 'image/png'].indexOf(f.type) === -1) {
            error.textContent = 'Format file harus .jpg atau .png.';
            error.hidden = false; input.value = ''; return;
        }
        if (f.size > MAKS) {
            error.textContent = 'Ukuran file maksimal 2 MB.';
            error.hidden = false; input.value = ''; return;
        }
        img.src = URL.createObjectURL(f);
        img.hidden = false;
        if (kosong) kosong.hidden = true;
    });
})();
