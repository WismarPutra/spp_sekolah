<header class="bg-white shadow px-6 py-6 flex justify-between items-center">

    <div class="flex items-center gap-4">

        <button class="open-sidebar md:hidden">
            ☰
        </button>
        <h1 class="font-semibold">
            @yield('contentUser')
        </h1>

    </div>

    <div class="flex items-center gap-4">
        @auth
        <span class="text-gray-600">
            {{ auth()->user()->name}}
        </span>

        <form action="{{ route('logout')}}" method="post">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Logout</button>
        </form>
        @else
        <a href="/login" class="bg-red-500 text-white px-3 py-1 rounded">
            Login
        </a>
        @endauth
    </div>

</header>