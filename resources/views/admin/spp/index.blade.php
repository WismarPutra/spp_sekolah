@extends('components.layouts.admin')

@section('content')
Data SPP
@endsection

@section('main')

<div class="p-4 md:p-6 w-full max-w-full">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Data SPP</h2>
        <a href="#create-spp"
            class="w-full sm:w-auto text-center bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium inline-block">
            Tambah SPP
        </a>
        @include('admin.spp.create')
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
        <p class="text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
        <p class="text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    {{-- TABLE CONTAINER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                            Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($spp as $item)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $item->tahun }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">Kelas {{ $item->kelas }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap uppercase">{{ $item->jurusan }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-3">
                                <a href="#edit-spp-{{ $item->id }}"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-3 py-1.5 rounded-lg text-xs font-bold transition inline-block">
                                    Edit
                                </a>

                                <form action="{{ route('spp.destroy', $item->id) }}" method="POST" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                            {{-- PANGGIL MODAL EDIT --}}
                            @include('admin.spp.edit')

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-400 italic text-sm">
                            Data SPP belum tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection