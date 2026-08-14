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

    <title>User</title>

</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div id="overlayUser" class="fixed inset-0 bg-black bg-opacity-40 hidden md:hidden"
            onclick="toggleSidebarUser()">
        </div>
        <x-navbar-user></x-navbar-user>

        <div class="flex-1 flex flex-col">

            <x-header-user>Admin</x-header-user>

            <main class="p-6 flex-1">
                <div>
                    @yield('mainUser')
                </div>
            </main>

        </div>

    </div>

</body>

</html>