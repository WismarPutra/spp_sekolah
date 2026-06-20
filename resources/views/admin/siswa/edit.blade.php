{{-- ================= POP-UP EDIT SISWA (100% TAILWIND) ================= --}}
<div id="edit-siswa-{{ $siswa->id }}"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center invisible opacity-0 scale-95 transition-all duration-200 target:visible target:opacity-100 target:scale-100">

    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg border border-gray-100 relative mx-4">

        {{-- Tombol Silang (Close) --}}
        <a href="#" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-semibold">&times;</a>

        <h2 class="text-lg font-bold text-gray-800 mb-4 text-left">Edit Data Siswa</h2>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm text-left">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ================= FORM ACCORDION / UTILITY: RESET & KIRIM ULANG AKUN ================= --}}
        <div class="mb-5 p-3 bg-amber-50 border border-amber-200 rounded-lg text-left">
            <p class="text-xs text-amber-800 mb-2 font-medium">Orang tua lupa password atau chat WA terhapus?</p>
            <form action="{{ route('admin.siswa.kirimUlangAkun', $siswa->id) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin mereset password dan mengirim ulang info akun ke nomor WA siswa ini?')">
                @csrf
                <button type="submit"
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold transition shadow-sm flex items-center justify-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset Password & Kirim ke WA
                </button>
            </form>
        </div>

        {{-- Action langsung mengarah ke route update siswa --}}
        <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" value="{{ $siswa->nis }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <input type="text" name="nama" value="{{ $siswa->nama }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10" {{ $siswa->kelas == 10 ? 'selected' : '' }}>10</option>
                    <option value="11" {{ $siswa->kelas == 11 ? 'selected' : '' }}>11</option>
                    <option value="12" {{ $siswa->kelas == 12 ? 'selected' : '' }}>12</option>
                </select>
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                <select name="tahun_masuk"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @php
                    $tahunSekarang = date('Y');
                    @endphp
                    @for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                    <option value="{{ $i }}" {{ ($siswa->tahun_masuk ?? $tahunSekarang) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                    @endfor
                </select>
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    <option value="otomotif" {{ strtolower($siswa->jurusan) == 'otomotif' ? 'selected' : '' }}>OTOMOTIF
                    </option>
                    <option value="akuntansi" {{ strtolower($siswa->jurusan) == 'akuntansi' ? 'selected' : '' }}>
                        AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" name="alamat" value="{{ $siswa->alamat }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-5 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP (WhatsApp)</label>
                <input type="text" name="no_hp" value="{{ $siswa->no_hp }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3">
                <a href="#"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>