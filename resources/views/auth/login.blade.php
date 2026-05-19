<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">

        <h2 class="text-2xl font-bold text-center mb-6">
            Login Sistem Pembayaran
        </h2>

        @if(session('error'))
        <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">

            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>

                <input
                    type="email"
                    name="email"
                    required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan email">

                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>


            <div class="mb-4">

                <label class="block text-sm font-medium mb-1">Password</label>

                <div class="relative">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full border rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan password">

                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-2 text-gray-500">

                        👁

                    </button>

                </div>

                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>


            <!-- <div class="flex items-center justify-between mb-4">

                <label class="flex items-center text-sm">

                    <input type="checkbox" name="remember" class="mr-2">

                    Remember me

                </label>

            </div> -->


            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">

                Login

            </button>

        </form>

    </div>


    <script>
        function togglePassword() {

            const password = document.getElementById('password');

            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }

        }
    </script>

</body>

</html>