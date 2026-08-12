
document.addEventListener("DOMContentLoaded", function () {

    // 2. Logika Modal Pemilihan Metode Pembayaran
    const modal = document.getElementById('paymentModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const confirmPayBtn = document.getElementById('confirmPayBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const modalBulanText = document.getElementById('modalBulanText');
    
    let currentPayUrl = '';

    function closeModal() {
        modal.classList.add('hidden');
        // Reset status tombol
        btnText.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
        confirmPayBtn.disabled = false;
        
        // Hapus pilihan radio
        const radios = document.querySelectorAll('input[name="payment_method"]');
        radios.forEach(radio => radio.checked = false);
    }

    // Tutup modal jika klik tombol batal/X
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);

    // Buka modal jika tombol 'Bayar Sekarang' diklik
    document.querySelectorAll('.open-modal-button').forEach(button => {
        button.addEventListener('click', function () {
            currentPayUrl = this.getAttribute('data-url');
            modalBulanText.textContent = this.getAttribute('data-tagihan-bulan');
            modal.classList.remove('hidden');
        });
    });

    // Proses konfirmasi pembayaran
    confirmPayBtn.addEventListener('click', function () {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu!');
            return;
        }

        // Tampilkan loading di tombol
        btnText.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        confirmPayBtn.disabled = true;

        const url = `${currentPayUrl}?method=${selectedMethod.value}`;

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
                if (data.snap_token) {
                    closeModal(); // Tutup modal buatan kita
                    
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            window.location.reload();
                        },
                        onPending: function(result){
                            window.location.reload();
                        },
                        onError: function(result){
                            alert("Gagal memproses pembayaran!");
                        },
                        onClose: function(){
                            // User menutup popup Snap tanpa aksi
                        }
                    });
                } else {
                    alert('Gagal mendapatkan Token pembayaran dari server.');
                    closeModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan saat memproses pembayaran.');
                closeModal();
            });
    });

});
