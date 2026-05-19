document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById('sidebarUser');
    const overlay = document.getElementById('overlay');

    // tombol buka sidebar
    const openButtons = document.querySelectorAll('.open-sidebar');

    // tombol tutup sidebar
    const closeButtons = document.querySelectorAll('.close-sidebar');

    // buka sidebar
    openButtons.forEach(button => {
        button.addEventListener('click', function () {
            sidebar.classList.remove('-translate-x-full');

            if (overlay) {
                overlay.classList.remove('hidden');
            }
        });
    });

    // tutup sidebar
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            sidebar.classList.add('-translate-x-full');

            if (overlay) {
                overlay.classList.add('hidden');
            }
        });
    });

});