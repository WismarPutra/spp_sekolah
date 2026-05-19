@extends('components.layouts.user')

@section('contentUser')
Pembayaran
@endsection

@section('mainUser')
<div class="min-h-screen bg-gray-50 p-4 md:p-8 flex justify-center">
    <div class="w-full max-w-5xl">
        <h1 class="text-xl md:text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Tagihan Saya</h1>

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
                            class="pay-button w-full sm:w-48 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-green-100 active:scale-95"
                            data-url="{{ route('user.pay', $item->id) }}">
                            Bayar Sekarang
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
                                <th class="p-4 text-left">Metode</th>
                                <th class="p-4 text-left">Total</th>
                                <th class="p-4 text-left">Tanggal</th>
                                <th class="p-4 text-center w-32">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($riwayat as $tagihan)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-4 text-center text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                <td class="p-4 font-semibold text-gray-700 uppercase">
                                    {{ $tagihan->pembayaran->metode ?? 'Online' }}
                                </td>
                                <td class="p-4 font-bold text-gray-800">
                                    Rp{{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-gray-500 font-medium">
                                    {{ $tagihan->updated_at->format('d/m/Y') }}
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