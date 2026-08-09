@extends('components.layouts.admin')

@section('content')
Tambah Siswa
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6 max-w-3xl mx-auto">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah Data Siswa Baru</h2>

        {{-- Menampilkan Pesan Error Validasi --}}
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <strong class="font-bold block mb-1">Terjadi Kesalahan!</strong>
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required minlength="8" maxlength="10" pattern="[0-9]*" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Contoh: 20260102">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required placeholder="Nama Lengkap Siswa">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" id="kelas"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>Pilih Kelas</option>
                        <option value="10" {{ old('kelas') == '10' ? 'selected' : '' }}>10</option>
                        <option value="11" {{ old('kelas') == '11' ? 'selected' : '' }}>11</option>
                        <option value="12" {{ old('kelas') == '12' ? 'selected' : '' }}>12</option>
                    </select>
                </div>

                <div>
                    <label for="tahun_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                    <select name="tahun_masuk" id="tahun_masuk"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @php $tahunSekarang = date('Y'); @endphp
                        @for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                        <option value="{{ $i }}" {{ (old('tahun_masuk') ?? $tahunSekarang) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan" id="jurusan"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="" disabled {{ old('jurusan') ? '' : 'selected' }}>Pilih Jurusan</option>
                    <option value="otomotif" {{ old('jurusan') == 'otomotif' ? 'selected' : '' }}>OTOMOTIF</option>
                    <option value="akuntansi" {{ old('jurusan') == 'akuntansi' ? 'selected' : '' }}>AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat') }}"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">No HP (WhatsApp)</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 081234567890" inputmode="tel" pattern="^(08|628)[0-9]{8,12}$" required>
                <small class="text-gray-500 mt-1 block">Pastikan nomor aktif untuk pengiriman akun via WA</small>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('siswa.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection