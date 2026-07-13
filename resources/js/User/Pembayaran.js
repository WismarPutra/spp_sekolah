
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
                    if (data.payment_url) {
                        loadJokulCheckout(data.payment_url);
                    } else {
                        alert('Gagal mendapatkan URL pembayaran.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                });
        });
    });

});
