@extends('components.layouts.admin')

@section('content')
Data Siswa
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Data Siswa</h2>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            {{-- Ubah Href menuju Route Create --}}
            <a href="{{ route('siswa.create') }}"
                class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm font-medium flex items-center justify-center">
                Tambah Siswa
            </a>
        </div>
    </div>

    {{-- Pesan Sukses / Error Global --}}
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">NIS</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Nama Siswa</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Tahun Masuk</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Kelas</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Jurusan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Alamat</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Kontak</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($urutanSiswa as $siswa)
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-600">{{ $siswa->nis }}</td>
                        <td class="px-4 py-4"><div class="text-sm font-bold text-gray-900">{{ $siswa->nama }}</div></td>
                        <td class="px-4 py-4"><div class="text-sm text-center text-gray-700">{{ $siswa->tahun_masuk ?? '-' }}</div></td>
                        <td class="px-4 py-4"><div class="text-sm text-gray-700 font-medium">Kelas {{ $siswa->kelas }}</div></td>
                        <td class="px-4 py-4"><div class="text-sm text-gray-700 uppercase">{{ $siswa->jurusan }}</div></td>
                        <td class="px-4 py-4"><p class="text-sm text-gray-600 truncate max-w-[200px]" title="{{ $siswa->alamat }}">{{ $siswa->alamat }}</p></td>
                        <td class="px-4 py-4 text-center">
                            <a href="https://wa.me/{{ $siswa->no_hp }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:underline">
                                {{ preg_replace('/^62/', '0', $siswa->no_hp) }}
                            </a>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Ubah Href Edit --}}
                                <a href="{{ route('siswa.edit', $siswa->nis) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xs font-bold rounded-lg transition duration-200">
                                    Edit
                                </a>

                                <form action="{{ route('siswa.destroy', $siswa->nis) }}" method="POST" class="form-delete" onsubmit="return confirm('Yakin ingin menghapus data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition duration-200 shadow-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection