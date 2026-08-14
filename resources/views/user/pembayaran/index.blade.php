@extends('components.layouts.user')

@section('contentUser')
Pembayaran
@endsection

@section('mainUser')
<div class="min-h-screen bg-gray-50 p-4 md:p-8 flex justify-center">
    <div class="w-full max-w-5xl">
        <!-- Bagian Header Profil Siswa -->
        <div class="mb-6 bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
            @if($siswa)
                <table class="text-sm md:text-base font-bold text-gray-800 uppercase">
                    <tbody>
                        <tr>
                            <td class="py-1 pr-4 w-24 md:w-32">NAMA</td>
                            <td class="py-1 px-2 text-center">:</td>
                            <td class="py-1">{{ $siswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4">KELAS</td>
                            <td class="py-1 px-2 text-center">:</td>
                            <td class="py-1">{{ $siswa->kelas }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4">PROGRAM</td>
                            <td class="py-1 px-2 text-center">:</td>
                            <td class="py-1">{{ $siswa->jurusan }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4 align-top">ALAMAT</td>
                            <td class="py-1 px-2 text-center align-top">:</td>
                            <td class="py-1 tracking-widest ">
                                {{ $siswa->alamat ?? '......................................................' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @else
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Tagihan Saya</h1>
            @endif
        </div>
        </h1>

        <div class="grid gap-4">
            @forelse ($tagihans as $item)
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="font-bold text-gray-800 text-lg">SPP {{ $item->bulan_text }} {{ $item->tahun }}
                            </h2>
                        </div>
                        <p class="text-blue-600 font-bold text-xl mb-2">Rp
                            {{ number_format($item->jumlah, 0, ',', '.') }}</p>

                        @if($item->status == 'lunas')
                        <span
                            class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-tight">Terbayar</span>
                        @else
                        <span
                            class="inline-block bg-red-50 text-red-600 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-tight">Menunggu
                            Pembayaran</span>
                        @endif
                    </div>

                    @if($item->status != 'lunas')
                    <div class="w-full sm:w-auto pt-2 sm:pt-0">
                        <button
                            class="pay-direct-btn w-full sm:w-48 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-green-100 active:scale-95 flex justify-center items-center gap-2"
                            data-url="{{ route('user.pay', $item->id) }}">
                            <span>Bayar Sekarang</span>
                            <svg class="btn-spinner hidden h-5 w-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white p-10 rounded-2xl text-center border-2 border-dashed border-gray-200">
                <p class="text-gray-400 font-medium">Semua tagihan sudah beres! 🎉</p>
            </div>
            @endforelse
        </div>

        <div class="mt-10">
            <h2 class="text-lg font-bold mb-4 text-gray-700 flex items-center gap-2">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                Riwayat Pembayaran
            </h2>

            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="bg-gray-50 text-gray-500 uppercase text-[10px] font-black tracking-widest border-b">
                                <th class="p-4 text-center w-16">No</th>
                                <th class="p-4 text-left">Bulan</th>
                                <th class="p-4 text-left">Total</th>
                                <th class="p-4 text-left">Tanggal</th>
                                <th class="p-4 text-left">Metode Pembayaran</th>
                                <th class="p-4 text-center w-32">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($riwayat as $tagihan)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-4 text-center text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                 <td class="p-4 text-gray-500 font-medium">
                                    {{ $tagihan->bulan}}
                                </td>
                                <td class="p-4 font-bold text-gray-800">
                                    Rp{{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-gray-500 font-medium">
                                    {{ $tagihan->updated_at->format('d/m/Y') }}
                                </td>
                                <td class="p-4 font-semibold text-gray-700 uppercase">
                                    {{ $tagihan->metode ?? 'Online' }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black tracking-wider shadow-sm">
                                        SUCCESS
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 italic bg-white">
                                    Belum ada data pembayaran yang tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection