document.addEventListener("DOMContentLoaded", function () {
    const siswaSelect = document.getElementById("siswa_select");
    const sectionSiswa = document.getElementById("section_siswa");
    const sectionSPP = document.getElementById("section_spp"); // Tambahkan ini
    const nominalInput = document.getElementById("nominal_input");
    const sppSelect = document.getElementById("spp_id_input");

    if (siswaSelect) {
        siswaSelect.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            const nominal = selectedOption.getAttribute("data-nominal");
            const sppId = selectedOption.getAttribute("data-sppid");

            document.getElementById("nominal_input").value = nominal;

            // ini penting, cek dulu ada atau tidak
            const sppInput = document.getElementById("spp_id_input");
            if (sppInput) {
                sppInput.value = sppId;
            }

            if (nominal == 0) {
                alert("Data SPP belum diatur!");
            }
        });
    }

    window.toggleTipe = function (tipe) {
        if (tipe === 'massal') {
            // Sembunyikan pilihan Siswa dan SPP
            sectionSiswa.style.display = 'none';
            sectionSPP.style.display = 'none';

            // Matikan validasi required agar form bisa dikirim
            siswaSelect.required = false;
            sppSelect.required = false;

            // Kosongkan nilai agar tidak mengirim data sampah
            nominalInput.value = "";
        } else {
            // Tampilkan kembali untuk pilihan individu
            sectionSiswa.style.display = 'block';
            sectionSPP.style.display = 'block';

            siswaSelect.required = true;
            sppSelect.required = true;
        }
    };

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("closeModalTagihan")) {
            modal.classList.add("hidden");
        }
    });
});