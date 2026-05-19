document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modal");
    const form = document.getElementById("formSpp");

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("openModal")) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }
    });

    document.querySelectorAll(".openEdit").forEach((btn) => {
        btn.addEventListener("click", function () {
            modal.classList.remove("hidden");
            modal.classList.add("flex");

            form.action = `/admin/spp/${this.dataset.id}`;
            document.getElementById("method").value = "PUT";

            document.getElementById("tahun").value = this.dataset.tahun;
            document.getElementById("kelas").value = this.dataset.kelas;
            document.getElementById("jurusan").value = this.dataset.jurusan;
            document.getElementById("nominal").value = this.dataset.nominal;
        });
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("closeModal")) {
            modal.classList.add("hidden");
        }
    });

    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
        }
    });

});
