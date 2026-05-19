<aside id="sidebar" class="fixed md:static z-50 w-64 bg-gray-900 text-white min-h-screen
transform -translate-x-full md:translate-x-0 transition-transform duration-200">

    <div class="flex justify-between items-center p-6 border-b border-gray-700">

        <span class="font-bold text-lg">ADMINISTRASI KEUANGAN SEKOLAH</span>

        <button onclick="toggleSidebar()" class="md:hidden">
            ✕
        </button>

    </div>

    <nav class="space-y-2">

        <a href="{{ route('admin.dashboard') }}" class="block p-2 rounded">
            Dashboard
        </a>

        <a href="{{ route('siswa.index')}}" class="block p-2 rounded">
            Data Siswa
        </a>

        <a href="{{ route('spp.index')}}" class="block p-2 rounded ">
            Data Iuran Praktek Komputer
        </a>

        <a href="/admin/tagihan" class="block p-2 rounded ">
            Tagihan
        </a>

    </nav>


</aside>