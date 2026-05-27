document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalEdit");

    document.querySelectorAll(".openModalEdit").forEach((button) => {
        button.addEventListener("click", function () {
            modal.classList.remove("hidden");
            modal.classList.add("flex");

            // isi form
            document.getElementById("nis").value = this.dataset.nis;
            document.getElementById("nama").value = this.dataset.nama;
            document.getElementById("kelas").value = this.dataset.kelas;
            document.getElementById("edit-jurusan").value = this.dataset.jurusan;
            document.getElementById("edit-tahun_masuk").value = this.dataset.tahun_masuk;
            document.getElementById("alamat").value = this.dataset.alamat;
            document.getElementById("no_hp").value = this.dataset.no_hp;

            // set action form
            document.getElementById("formEdit").action =
                `/admin/siswa/${this.dataset.id}`;
        });
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("closeModalEdit")) {
            modal.classList.add("hidden");
        }
    });
});
