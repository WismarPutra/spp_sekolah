document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function (e) {
        // 1. Tahan agar form tidak langsung submit ke controller Laravel
        e.preventDefault();

        // 2. Munculkan SweetAlert2 custom
        Swal.fire({
            title: 'Apakah Kamu Yakin?',
            text: "Ingin menghapus data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1', // Warna ungu tombol YA (sesuaikan dengan tema Anda)
            cancelButtonColor: '#4b5563',  // Warna abu-abu tombol TIDAK
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'TIDAK',
            reverseButtons: false // Mengatur posisi tombol kiri-kanan
        }).then((result) => {
            // 3. Jika user menekan tombol 'YA, HAPUS!'
            if (result.isConfirmed) {
                form.submit(); // Teruskan submit form ke route Laravel
            }
        });
    });
});
