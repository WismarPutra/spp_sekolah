
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
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan pada server');
                    }
                    return data;
                })
                .then(data => {
                    if (data.payment_url) {
                        loadJokulCheckout(data.payment_url);
                    } else {
                        alert('Gagal mendapatkan URL pembayaran.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Terjadi kesalahan saat memproses pembayaran.');
                });
        });
    });

});
