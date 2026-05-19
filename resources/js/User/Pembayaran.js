
document.addEventListener("DOMContentLoaded", function () {

    // 2. Logika AJAX untuk menampilkan Snap
    document.querySelectorAll('.pay-button').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Memanggil popup Snap
                    window.snap.pay(data.snap_token, {
                        onSuccess: function (result) {
                            alert("Pembayaran berhasil!");
                            location.reload();
                        },
                        onPending: function (result) {
                            alert("Menunggu pembayaran Anda.");
                            location.reload();
                        },
                        onError: function (result) {
                            alert("Pembayaran gagal!");
                            location.reload()
                        },
                        onClose: function () {
                            alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                });
        });
    });

});
