@extends('components.layouts.admin')

@section('content')
Edit Data Siswa
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6 max-w-3xl mx-auto">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Edit Data Siswa</h2>

        {{-- Menampilkan Pesan Error Validasi --}}
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            <strong class="font-bold block mb-1">Periksa kembali data Anda:</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Pesan Sukses / Error untuk Reset Akun --}}
        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            {{ session('error') }}
        </div>
        @endif

        {{-- Fitur Reset & Kirim Ulang Akun --}}
        <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm text-amber-800 mb-3 font-medium">Orang tua lupa password atau chat WA terhapus?</p>
            <form action="{{ route('admin.siswa.kirimUlangAkun', $siswa->nis) }}" method="POST"
                onsubmit="return confirm('Yakin ingin mereset password dan mengirim info akun ke WA siswa ini?')">
                @csrf
                <button type="submit"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset Password & Kirim ke WA
                </button>
            </form>
        </div>

        <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                {{-- old() diprioritaskan, jika tidak ada pakai $siswa->nis --}}
                <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" required minlength="4" maxlength="10" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10" {{ old('kelas', $siswa->kelas) == 10 ? 'selected' : '' }}>10</option>
                        <option value="11" {{ old('kelas', $siswa->kelas) == 11 ? 'selected' : '' }}>11</option>
                        <option value="12" {{ old('kelas', $siswa->kelas) == 12 ? 'selected' : '' }}>12</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                    <select name="tahun_masuk" class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @php $tahunSekarang = date('Y'); @endphp
                        @for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                        <option value="{{ $i }}" {{ old('tahun_masuk', $siswa->tahun_masuk ?? $tahunSekarang) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan" class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    <option value="otomotif" {{ old('jurusan', strtolower($siswa->jurusan)) == 'otomotif' ? 'selected' : '' }}>OTOMOTIF</option>
                    <option value="akuntansi" {{ old('jurusan', strtolower($siswa->jurusan)) == 'akuntansi' ? 'selected' : '' }}>AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat', $siswa->alamat) }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP (WhatsApp)</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}" required inputmode="tel" pattern="^(08|628)[0-9]{8,12}$"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('siswa.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection