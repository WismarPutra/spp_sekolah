@extends('components.layouts.admin')

@section('content')
Pembayaran
@endsection

@section('main')
<div class="p-6">
    <a href="{{ route('laporan.export', ['bulan' => request('bulan')]) }}"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg btn btn-success">
        Download CSV (Excel)
    </a>

    <div class="bg-white shadow rounded-xl mt-6">

        <table class="w-full">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Kelas</th>
                    <th class="p-3">Metode</th>
                    <th class="p-3">Jumlah</th>
                    <th class="p-3">Tanggal Bayar</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>

            <tbody>

                @foreach($pembayaran as $bayar)

                <tr class="border-b ">

                    <td class="p-3 text-center">{{ $loop->iteration }}</td>
                    <td class="p-3 text-center">{{ $bayar->tagihan->siswa->nama ?? 'Data Siswa Tidak Ditemukan' }}</td>
                    <td class="p-3 text-center">{{ $bayar->tagihan->siswa->kelas ?? '-' }}</td>
                    <td class="p-3 text-center">{{ $bayar->metode }}</td>
                    <td class="p-3 text-center">{{ $bayar->jumlah }}</td>
                    <td class="p-3 text-center">{{ $bayar->tanggal_bayar }}</td>
                    <td class="p-3 text-center">{{ $bayar->status }}</td>


                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
@endsection