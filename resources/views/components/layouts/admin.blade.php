<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <link rel="icon" type="image/x-icon" href="{{ asset('logoSmk.jpg')}}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Admin</title>
</head>

<body class="bg-gray-100 antialiased">
    <!-- Tambahkan min-h-screen dan w-full agar kontainer utama terkunci -->
    <div class="flex min-h-screen w-full overflow-hidden">

        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden z-40 md:hidden" onclick="toggleSidebar()">
        </div>

        <x-navbar-admin></x-navbar-admin>

        <!-- Tambahkan w-0 dan flex-1 agar kolom konten tidak mendorong sidebar keluar layar -->
        <div class="flex-1 flex flex-col w-0">

            <x-header-admin>Admin</x-header-admin>

            <!-- 
                1. Ubah p-6 menjadi p-4 md:p-6 (Lebih kecil di HP)
                2. Tambahkan overflow-x-hidden agar halaman tidak bisa geser ke kanan
                3. Tambahkan overflow-y-auto agar konten tetap bisa scroll ke bawah
            -->
            <main class="p-4 md:p-6 flex-1 overflow-x-hidden overflow-y-auto">
                <div class="max-w-full">
                    <!-- Memastikan konten di dalam yield tidak meluap -->
                    @yield('main')
                </div>
            </main>

        </div>
    </div>
</body>

</html>