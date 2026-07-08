<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SMK Utama Cianjur</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="images-removebg-preview.png" type="image/x-icon" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />

    <style>
    body {
        font-family: "Inter", sans-serif;
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- HEADER -->
    <header class="bg-white shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 py-4">

            <!-- Flex Responsive -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                <!-- Logo / Judul -->
                <h1 class="text-2xl font-bold text-blue-700 text-center md:text-left">
                    SMK Utama Cianjur
                </h1>

                <!-- Navigation -->
                <nav class="flex flex-wrap justify-center gap-4 text-sm md:text-base">
                    <a href="#beranda" class="text-gray-700 hover:text-blue-600 transition">
                        Beranda
                    </a>

                    <a href="#profil" class="text-gray-700 hover:text-blue-600 transition">
                        Profil
                    </a>

                    <a href="#kontak" class="text-gray-700 hover:text-blue-600 transition">
                        Kontak
                    </a>

                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition">
                        Pembayaran SPP
                    </a>
                </nav>

            </div>
        </div>
    </header>

    <!-- HERO -->
    <section id="beranda" class="bg-blue-100 py-16 md:py-20">
        <div class="max-w-4xl mx-auto px-4 text-center">

            <h2 class="text-3xl md:text-5xl font-bold mb-6 text-blue-800 leading-tight">
                Selamat Datang di SMK Utama Cianjur
            </h2>

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/' . $nama) }}" alt="Logo SMK" class="w-40 md:w-56 lg:w-64 h-auto">
            </div>

            <p class="text-base md:text-lg text-blue-700 mb-6">
                Membangun masa depan cerah dengan pendidikan berkualitas
            </p>

            <!-- Tombol -->
            <!--
            <a href="{{ route('login') }}"
                class="inline-block bg-blue-600 text-white px-6 py-3 rounded-full text-lg hover:bg-blue-700 transition">
                Masuk Dashboard
            </a>
            -->

        </div>
    </section>

    <!-- PROFIL -->
    <section id="profil" class="py-14 md:py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 text-center">

            <h3 class="text-2xl md:text-3xl font-bold mb-4">
                Tentang Kami
            </h3>

            <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                SMK Utama Cianjur adalah sekolah kejuruan yang fokus pada
                pengembangan keterampilan dan pengetahuan siswa dalam bidang
                otomotif dan akuntansi. Kami berkomitmen
                untuk mencetak lulusan yang siap kerja dan kompeten.
            </p>

        </div>
    </section>

    <!-- KONTAK -->
    <section id="kontak" class="py-14 md:py-16 bg-gray-100">
        <div class="max-w-5xl mx-auto px-4 text-center">

            <h3 class="text-2xl md:text-3xl font-bold mb-4">
                Hubungi Kami
            </h3>

            <p class="text-gray-600 mb-6 text-sm md:text-base leading-relaxed">
                Alamat: JL. RAYA BANDUNG, KM 03 CIRANJANG - CIANJUR CIBIUK,
                Kec. Ciranjang Kab. Cianjur, Prov. Jawa Barat Kode Pos: 43282
                | Telp: 02632324048
            </p>

            <a href="mailto:info@smkutamacianjur.sch.id" class="text-blue-600 underline break-all">
                info@smkutamacianjur.sch.id
            </a>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white text-center py-6 text-sm text-gray-500 border-t">
        &copy; 2025 SMK Utama Cianjur. All rights reserved.
    </footer>

</body>

</html>