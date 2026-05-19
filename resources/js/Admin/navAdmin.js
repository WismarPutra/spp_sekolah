window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar'); // Pastikan di <x-navbar-admin> id-nya 'sidebar'
    const overlay = document.getElementById('overlay');

    if (sidebar && overlay) {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
}