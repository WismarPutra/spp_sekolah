@extends('components.layouts.admin')

@section('content')
Tagihan
@endsection

@section('main')
<div class="p-6">

    {{-- HEADER & TOMBOL TAMBAH --}}
    <div class="flex justify-between items-center mb-6">
        {{-- Menggunakan Anchor murni untuk membuka modal --}}
        <a href="#modal-tambah-tagihan"
            class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-blue-700 transition shadow-sm inline-block">
            Tambah Tagihan
        </a>
        @include('admin.tagihan.create')
    </div>

    {{-- NOTIFIKASI SISI SERVER --}}
    @if(session('success'))
    <div class="bg-green-500 text-white p-3 mb-4 rounded-lg text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500 text-white p-3 mb-4 rounded-lg text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    {{-- FILTER DATA --}}
    <form action="{{ route('tagihan.index') }}" method="GET"
        class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap items-end gap-4">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Bulan</label>
            <select name="bulan"
                class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                <option value="">Semua Bulan</option>
                @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                    @endfor
            </select>
        </div>

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Jurusan</label>
            <select name="jurusan"
                class="w-full border border-gray-200 p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                <option value="">Semua Jurusan</option>
                @foreach($daftarJurusan as $jur)
                <option value="{{ $jur }}" {{ request('jurusan') == $jur ? 'selected' : '' }}>
                    {{ strtoupper($jur) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit"
                class="flex-1 md:flex-none bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
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

    {{-- TABEL DATA TAGIHAN --}}
    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Siswa</th>
                    <th class="p-3">Kelas</th>
                    <th class="p-3">Jurusan</th>
                    <th class="p-3">SPP</th>
                    <th class="p-3">Bulan</th>
                    <th class="p-3">Tahun</th>
                    <th class="p-3">Nominal</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                @forelse ($ambilDataTagihan as $item)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3 font-medium text-gray-900">{{ $item->siswa->nama }}</td>
                    <td class="p-3 font-medium text-gray-900">{{ $item->siswa->kelas }}</td>
                    <td class="p-3 font-medium text-gray-900">{{ $item->siswa->jurusan }}</td>
                    <td class="p-3">Rp {{ number_format($item->spp->nominal) }}</td>
                    <td class="p-3">{{ $item->bulan_text }}</td>
                    <td class="p-3">{{ $item->tahun }}</td>
                    <td class="p-3 font-semibold text-blue-600">Rp {{ number_format($item->jumlah) }}</td>

                    <td class="p-3">
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status == 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item->status == 'lunas' ? 'Lunas' : 'Belum' }}
                        </span>
                    </td>

                    <td class="p-3 flex gap-2 justify-center items-center">
                        {{-- Form Hapus dengan Native Confirm --}}
                        <form action="{{ route('tagihan.destroy', $item->id) }}" method="POST" class="form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded text-white text-xs font-medium transition shadow-sm">
                                Hapus
                            </button>
                        </form>

                        {{-- Logika Tombol WA Reminder Sisi Server --}}
                        @if($item->status == 'belum')
                        @php
                        $jumlahTunggakan = $ambilDataTagihan->where('siswa_id', $item->siswa_id)->count();
                        $tanggalDibuat = \Carbon\Carbon::parse($item->created_at);
                        $sudahLewat10Hari = \Carbon\Carbon::now()->gte($tanggalDibuat);

                        // Mengatur pesan konfirmasi konvensional vs darurat dari PHP
                        $pesanAlert = "Kirim pengingat WA biasa ke siswa: " . $item->siswa->nama . "?";
                        if ($jumlahTunggakan >= 2) {
                        $pesanAlert = "⚠️ PERINGATAN KERAS:\\nSiswa bernama " . $item->siswa->nama . " menunggak selama
                        " . $jumlahTunggakan . " bulan.\\n\\nSistem akan mengirimkan SURAT PANGGILAN ORANG TUA langsung
                        ke WhatsApp resmi.\\n\\nApakah Anda yakin ingin melanjutkan?";
                        }
                        @endphp

                        @if($sudahLewat10Hari)
                        <a href="{{ route('tagihan.reminder', $item->id) }}"
                            onclick="return confirm('{{ $pesanAlert }}')"
                            class="px-2 py-1.5 text-white rounded text-xs font-medium shadow-sm transition {{ $jumlahTunggakan >= 2 ? 'bg-red-600 hover:bg-red-700' : 'bg-orange-500 hover:bg-orange-600' }}">
                            <i class="fas fa-paper-plane"></i>
                            {{ $jumlahTunggakan >= 2 ? 'PANGGIL (Tunggakan >= 2)' : 'REMINDER WA' }}
                        </a>
                        @else
                        <button disabled
                            class="px-2 py-1.5 bg-gray-200 text-gray-400 rounded text-xs flex items-center gap-1 cursor-not-allowed"
                            title="Belum mencapai batas 10 hari sejak tagihan terbit">
                            <i class="fas fa-clock"></i> Belum 10 Hari
                        </button>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center p-6 text-gray-400 italic">
                        Belum ada data tagihan menunggak yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection