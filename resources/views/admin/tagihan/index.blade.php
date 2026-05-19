@extends('components.layouts.admin')

@section('content')
Tagihan
@endsection

@section('main')
<div class="p-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">

        <button class="bg-blue-600 text-white px-4 py-2 rounded openModal">
            Tambah Tagihan
        </button>
        @include('admin.tagihan.create')
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="bg-green-500 text-white p-2 mb-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    {{-- FILTER --}}
    <form action="{{ route('tagihan.index') }}" method="GET"
        class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap items-end gap-4">

        <!-- Filter Bulan -->
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Bulan</label>
            <select name="bulan"
                class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                <option value="">Semua Bulan</option>
                @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                    @endfor
            </select>
        </div>

        <!-- Filter Jurusan -->
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Jurusan</label>
            <select name="jurusan"
                class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                <option value="">Semua Jurusan</option>
                @foreach($daftarJurusan as $jur)
                <option value="{{ $jur }}" {{ request('jurusan') == $jur ? 'selected' : '' }}>
                    {{ strtoupper($jur) }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit"
                class="flex-1 md:flex-none bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                Cari
            </button>

            @if(request('bulan') || request('jurusan'))
            <a href="{{ route('tagihan.index') }}"
                class="flex-1 md:flex-none text-center bg-gray-100 text-gray-600 px-6 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition">
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Siswa</th>
                    <th class="p-3">SPP</th>
                    <th class="p-3">Bulan</th>
                    <th class="p-3">Tahun</th>
                    <th class="p-3">Nominal</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($ambilDataTagihan as $item)
                <tr class="border-t">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3">{{ $item->siswa->nama }}</td>
                    <td class="p-3">{{ $item->spp->nominal }}</td>
                    <td class="p-3">{{ $item->bulan_text }}</td>
                    <td class="p-3">{{ $item->tahun }}</td>
                    <td class="p-3">Rp {{ number_format($item->jumlah) }}</td>

                    <td class="p-3">
                        @if($item->status == 'lunas')
                        <span class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs">
                            Lunas
                        </span>
                        @else
                        <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs">
                            Belum
                        </span>
                        @endif
                    </td>

                    <td class="p-3 flex gap-2 justify-center">
                        <button class="bg-yellow-400 px-3 py-1 rounded text-white openModalEdit">
                            Edit
                        </button>
                        @include('admin.tagihan.edit')

                        <form action="{{ route('tagihan.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 px-3 py-1 rounded text-white">
                                Hapus
                            </button>
                        </form>

                        @if($item->status == 'belum')
                        @php
                        $jumlahTunggakan = $ambilDataTagihan->where('siswa_id', $item->siswa_id)->count();
                        @endphp

                        <a href="{{ route('tagihan.reminder', $item->id) }}"
                            class="btn-reminder px-2 py-1 {{ $jumlahTunggakan >= 2 ? 'bg-red-600' : 'bg-green-600' }} text-white rounded text-xs flex items-center gap-1"
                            data-nama="{{ $item->siswa->nama }}" data-tunggakan="{{ $jumlahTunggakan }}">
                            <i class="fas fa-paper-plane"></i>
                            {{ $jumlahTunggakan >= 2 ? 'PANGGIL' : 'WA' }}
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center p-4 text-gray-500">
                        Data kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection