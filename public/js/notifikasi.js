/**
 * Notifikasi pop-up Istana Tembakau POS (berbasis SweetAlert2).
 *
 *  notif.sukses('Pesan')            -> toast hijau di pojok kanan atas
 *  notif.gagal('Pesan')             -> dialog merah
 *  notif.info('Pesan')              -> toast biasa
 *  notif.peringatanStok([...])      -> dialog daftar produk stok menipis
 *  <form data-konfirmasi-hapus="Nama item"> -> otomatis minta konfirmasi sebelum submit
 */
(function () {
    if (typeof Swal === 'undefined') {
        // CDN gagal dimuat: tetap berfungsi dengan dialog bawaan browser
        window.notif = {
            sukses: (m) => console.log(m),
            gagal: (m) => alert(m),
            info: (m) => console.log(m),
            waspada: (m) => alert(m),
            peringatan: (m) => alert(m),
            peringatanStok: () => {},
            konfirmasi: (o) => Promise.resolve(confirm(o.teks || o.judul)),
        };
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.dataset && form.dataset.konfirmasiHapus !== undefined && !confirm('Hapus ' + form.dataset.konfirmasiHapus + '?')) {
                e.preventDefault();
            }
        });
        return;
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        showCloseButton: true,
        timer: 3500,
        timerProgressBar: true,
        customClass: { popup: 'it-toast' },
        didOpen: (el) => {
            el.addEventListener('mouseenter', Swal.stopTimer);
            el.addEventListener('mouseleave', Swal.resumeTimer);
        },
    });

    const dialogDasar = {
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            popup: 'it-dialog',
            confirmButton: 'btn btn-emas px-4',
            cancelButton: 'btn btn-light px-4',
            denyButton: 'btn btn-outline-secondary px-4',
            actions: 'gap-2',
        },
    };

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    window.notif = {
        sukses(pesan, judul = 'Berhasil!') {
            return Toast.fire({ icon: 'success', title: judul, text: pesan });
        },

        waspada(pesan, judul = 'Perhatian') {
            return Toast.fire({ icon: 'warning', title: judul, text: pesan });
        },

        info(pesan, judul = 'Info') {
            return Toast.fire({ icon: 'info', title: judul, text: pesan });
        },

        peringatan(pesan, judul = 'Perhatian') {
            return Swal.fire({ ...dialogDasar, icon: 'warning', title: judul, text: pesan, confirmButtonText: 'Mengerti' });
        },

        gagal(pesan, judul = 'Oops, gagal') {
            return Swal.fire({ ...dialogDasar, icon: 'error', title: judul, html: pesan, confirmButtonText: 'Tutup' });
        },

        konfirmasi({ judul, teks, tombol = 'Ya, lanjutkan', ikon = 'question', bahaya = false }) {
            return Swal.fire({
                ...dialogDasar,
                icon: ikon,
                title: judul,
                html: teks,
                showCancelButton: true,
                confirmButtonText: tombol,
                cancelButtonText: 'Batal',
                focusCancel: bahaya,
                customClass: {
                    ...dialogDasar.customClass,
                    confirmButton: bahaya ? 'btn btn-danger px-4' : 'btn btn-emas px-4',
                },
            }).then((r) => r.isConfirmed);
        },

        /**
         * produk: [{nama, kode, stok, stok_minimum}], opsi: {judul, pengantar, urlStok}
         */
        peringatanStok(produk, opsi = {}) {
            if (!produk || produk.length === 0) return Promise.resolve();

            const baris = produk.map((p) => {
                const habis = p.stok <= 0;
                const persen = p.stok_minimum > 0 ? Math.max(4, Math.min(100, Math.round((p.stok / p.stok_minimum) * 100))) : 0;
                return `
                    <li class="it-stok-item">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="text-start">
                                <div class="fw-semibold">${esc(p.nama)}</div>
                                <div class="small text-muted">${esc(p.kode)} &middot; minimum ${p.stok_minimum}</div>
                            </div>
                            <span class="badge ${habis ? 'bg-danger' : 'bg-warning text-dark'}">${habis ? 'Habis' : 'Sisa ' + p.stok}</span>
                        </div>
                        <div class="it-stok-bar"><span style="width:${persen}%" class="${habis ? 'habis' : ''}"></span></div>
                    </li>`;
            }).join('');

            return Swal.fire({
                ...dialogDasar,
                iconHtml: '<i class="bi bi-box-seam"></i>',
                customClass: { ...dialogDasar.customClass, icon: 'it-icon-stok', popup: 'it-dialog it-dialog-stok' },
                title: opsi.judul || 'Stok Perlu Diisi Ulang',
                html: `<p class="text-muted mb-3">${esc(opsi.pengantar || 'Produk berikut sudah mencapai batas stok minimum:')}</p>
                       <ul class="it-stok-list">${baris}</ul>`,
                showCancelButton: !!opsi.urlStok,
                confirmButtonText: opsi.urlStok ? '<i class="bi bi-box-arrow-in-down"></i> Catat Barang Masuk' : 'Mengerti',
                cancelButtonText: 'Nanti saja',
            }).then((r) => {
                if (r.isConfirmed && opsi.urlStok) window.location.href = opsi.urlStok;
            });
        },
    };

    // Konfirmasi hapus untuk semua form bertanda data-konfirmasi-hapus
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.dataset || form.dataset.konfirmasiHapus === undefined || form.dataset.terkonfirmasi) return;

        e.preventDefault();
        const nama = form.dataset.konfirmasiHapus;
        const catatan = form.dataset.catatanHapus;

        notif.konfirmasi({
            judul: 'Hapus data ini?',
            teks: `<b>${esc(nama)}</b> akan dihapus permanen.` +
                  (catatan ? `<div class="small text-muted mt-2">${esc(catatan)}</div>` : '') +
                  '<div class="small text-danger mt-2"><i class="bi bi-exclamation-triangle"></i> Tindakan ini tidak bisa dibatalkan.</div>',
            tombol: '<i class="bi bi-trash"></i> Ya, hapus',
            ikon: 'warning',
            bahaya: true,
        }).then((ya) => {
            if (!ya) return;
            form.dataset.terkonfirmasi = '1';
            Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), customClass: { popup: 'it-dialog' } });
            form.submit();
        });
    });
})();
