
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
                    // PERBAIKAN: Gunakan token_id, bukan payment_url
                    if (data.token_id) {
                        loadJokulCheckout(data.token_id);
                    } else {
                        alert('Gagal mendapatkan Token pembayaran dari server.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Terjadi kesalahan saat memproses pembayaran.');
                });
        });
    });

});
