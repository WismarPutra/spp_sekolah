document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal');
    const closeBtn = document.getElementById('closeModal');

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("openModal")) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }
    });
    
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("closeModalSiswa")) {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
        }
    });

    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    });

})