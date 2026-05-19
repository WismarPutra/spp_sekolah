@extends('components.layouts.admin')

@section('content')
Data Siswa
@endsection

@section('main')
<div class="p-4 md:p-6 space-y-6">

    <!-- HEADER & ACTION -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Data Siswa</h2>
        <form action="{{ route('admin.siswa.reset') }}" method="POST"
            onsubmit="return confirm('Reset semua status kirim siswa?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-sync"></i> Reset Status Bulan Baru
            </button>
        </form>
        <button
            class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm font-medium openModal flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Tambah Siswa
        </button>

    </div>

    @include('admin.siswa.create')

    <!-- TABLE CONTAINER -->
    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            NIS</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Informasi Siswa</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Kelas & Jurusan</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            Alamat</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Kontak</th>
                        <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($urutanSiswa as $siswa)
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-600">
                            {{ $siswa->nis }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $siswa->nama }}</div>
                            <div class="text-xs text-gray-400">Tahun Masuk: {{ $siswa->tahun_masuk ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-700 font-medium">Kelas {{ $siswa->kelas }}</div>
                            <div class="text-xs text-gray-500">{{ $siswa->jurusan }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-[200px]" title="{{ $siswa->alamat }}">
                                {{ $siswa->alamat }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="https://wa.me/{{ $siswa->no_hp }}" target="_blank"
                                class="inline-flex items-center text-sm text-blue-600 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.48 5.228 3.48 8.404c0 6.556-5.332 11.888-11.888 11.888-2.01 0-3.987-.508-5.742-1.472l-6.141 1.697zm6.224-3.52c1.54.914 3.51 1.503 5.519 1.503 5.623 0 10.196-4.573 10.196-10.196s-4.573-10.196-10.196-10.196-10.196 4.573-10.196 10.196c0 2.115.633 4.12 1.83 5.812l-1.077 3.936 4.032-1.112z" />
                                </svg>
                                {{ preg_replace('/^62/', '0', $siswa->no_hp) }}
                            </a>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    class="openModalEdit inline-flex items-center px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xs font-bold rounded-lg transition duration-200"
                                    data-id="{{ $siswa->id }}" data-nis="{{ $siswa->nis }}"
                                    data-nama="{{ $siswa->nama }}" data-kelas="{{ $siswa->kelas }}"
                                    data-jurusan="{{ $siswa->jurusan }}" data-alamat="{{ $siswa->alamat }}"
                                    data-no_hp="{{ $siswa->no_hp }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L11.707 15.5a1 1 0 01-.414.263l-4 1a1 1 0 01-1.213-1.213l1-4a1 1 0 01.263-.414l8.5-8.5z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>

                                <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition duration-200 shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
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

{{-- Include modal edit di luar loop table agar tidak duplikasi elemen berat --}}
@include('admin.siswa.edit')

@endsection