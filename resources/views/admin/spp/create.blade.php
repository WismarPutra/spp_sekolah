@extends('components.layouts.admin')

@section('content')
Tambah SPP
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6 max-w-2xl mx-auto">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah Data SPP Baru</h2>

        {{-- Menampilkan Pesan Error dari validasi default atau with('error') --}}
        @if ($errors->any() || session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            @if(session('error'))
                <strong class="font-bold">{{ session('error') }}</strong>
            @endif
            @if($errors->any())
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @endif

        <form action="{{ route('spp.store')}}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" id="tahun"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @php $tahunSekarang = date('Y'); @endphp
                        @for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                        <option value="{{ $i }}" {{ (old('tahun') ?? $tahunSekarang) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" id="kelas"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>Pilih Kelas</option>
                        @for ($i = 10; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('kelas') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan" id="jurusan"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled {{ old('jurusan') ? '' : 'selected' }}>Pilih Jurusan</option>
                    <option value="otomotif" {{ old('jurusan') == 'otomotif' ? 'selected' : '' }}>OTOMOTIF</option>
                    <option value="akuntansi" {{ old('jurusan') == 'akuntansi' ? 'selected' : '' }}>AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" id="nominal" required value="{{ old('nominal') }}" placeholder="Contoh: 150000"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('spp.index') }}"
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