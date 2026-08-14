document.addEventListener("DOMContentLoaded", function () {

    // Logika Pembayaran Langsung (Tanpa Modal)
    document.querySelectorAll('.pay-direct-btn').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const btnText = this.querySelector('span');
            const btnSpinner = this.querySelector('.btn-spinner');

            // Tampilkan loading
            this.disabled = true;
            if (btnText) btnText.classList.add('hidden');
            if (btnSpinner) btnSpinner.classList.remove('hidden');

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
                    if (data.redirect_url) {
                        // Redirect ke halaman pembayaran Pakasir
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Gagal mendapatkan URL pembayaran dari server.');
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Terjadi kesalahan saat memproses pembayaran.');
                    resetButton();
                });

            function resetButton() {
                button.disabled = false;
                if (btnText) btnText.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
            }
        });
    });

});
