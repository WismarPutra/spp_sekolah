@extends('components.layouts.admin')

@section('content')
Dashboard Admin
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6">

    <!-- STATS CARDS: Grid otomatis berubah dari 1 kolom (HP) ke 4 kolom (Laptop) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 transition-transform hover:scale-[1.02]">
            <a href="/admin/siswa">
                <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $totalSiswa }}</p>
            </a>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 transition-transform hover:scale-[1.02]">
            <p class="text-sm text-gray-500 font-medium">Total Tagihan</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $totalTagihan }}</p>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 transition-transform hover:scale-[1.02]">
            <p class="text-sm text-gray-500 font-medium">Pembayaran Berhasil</p>
            <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $totalPembayaran }}</p>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 transition-transform hover:scale-[1.02]">
            <a href="/admin/tagihan">
                <p class="text-sm text-gray-500 font-medium">Tunggakan</p>
                <p class="text-2xl md:text-3xl font-bold text-red-600">{{ $totalTunggakan }}</p>
            </a>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-lg font-semibold text-gray-700">Riwayat Pembayaran Terbaru</h2>
        <a href="{{ route('admin.dashboard.export', ['bulan' => request('bulan')]) }}"
            class="inline-flex items-center justify-center w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a2 2 0 002 2h12 a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Excel
        </a>
    </div>

    <!-- TABLE CONTAINER: Menjaga layout tetap rapi di layar kecil -->
    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto overflow-y-hidden">
            <table class="w-full min-w-[1000px] divide-y divide-gray-200">
                <!-- min-w memastikan kolom tidak dempet di HP -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            No</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Nama Siswa</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Tahun Masuk</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Kelas </th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Jurusan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            SPP Bulan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Metode</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                            Jumlah</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Tanggal Bayar</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Status</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pembayaran as $bayar)
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="px-4 py-4 text-center text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $bayar->tagihan->siswa->nama ?? 'Data Tidak Ditemukan' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-700">
                                {{ $bayar->tagihan->siswa->tahun_masuk ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-700 font-medium">{{ $bayar->tagihan->siswa->kelas ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-700">{{ $bayar->tagihan->siswa->jurusan ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-sm text-gray-700 font-medium">{{$bayar->tagihan->bulan_text ?? '-'}}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2 py-1 text-[10px] font-bold bg-gray-100 text-gray-600 rounded">
                                {{ strtoupper($bayar->metode) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">
                            Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}
                            <span
                                class="block text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('H:i') }}
                                WIB</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                            $statusColor = $bayar->status == 'paid' ? 'bg-green-100 text-green-700 border-green-200' :
                            'bg-red-100 text-red-700 border-red-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColor }}">
                                <span
                                    class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $bayar->status == 'paid' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ strtoupper($bayar->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 0h6">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada riwayat pembayaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection