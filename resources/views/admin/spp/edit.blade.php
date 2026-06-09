{{-- ================= POP-UP EDIT MODAL (100% TAILWIND) ================= --}}
<div id="edit-spp-{{ $item->id }}"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center invisible opacity-0 scale-95 transition-all duration-200 target:visible target:opacity-100 target:scale-100">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 w-full max-w-md relative mx-4">

        {{-- Tombol Silang (Close) --}}
        <a href="#" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-semibold">&times;</a>

        <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Data SPP</h3>

        <form action="{{ route('spp.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="text" name="tahun" value="{{ $item->tahun }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <input type="text" name="kelas" value="{{ $item->kelas }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <input type="text" name="jurusan" value="{{ $item->jurusan }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
            </div>

            <div class="mb-5 text-left">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                <input type="number" name="nominal" value="{{ $item->nominal }}" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3">
                <a href="#"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>