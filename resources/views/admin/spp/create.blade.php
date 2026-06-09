{{-- Ganti id="modal" menjadi id="create-spp" dan terapkan kelas target Tailwind --}}
<div id="create-spp"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center invisible opacity-0 scale-95 transition-all duration-200 target:visible target:opacity-100 target:scale-100">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 w-full max-w-md relative mx-4">

        {{-- Tombol Silang (Close) --}}
        <a href="#" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-semibold">&times;</a>

        <h2 class="text-lg font-bold text-gray-800 mb-4">Form Tambah SPP</h2>

        <form action="{{ route('spp.store')}}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" id="tahun"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @php
                    $tahunSekarang = date('Y');
                    @endphp

                    @for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--)
                    <option value="{{ $i }}" {{ (old('tahun') ?? $tahunSekarang) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                    @endfor
                </select>
            </div>

            <div class="mb-4">
                <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan" id="jurusan"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Pilih Jurusan</option>
                    <option value="otomotif">OTOMOTIF</option>
                    <option value="akuntansi">AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas" id="kelas"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for ($i = 10; $i <= 12; $i++) <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                </select>
            </div>

            <div class="mb-5">
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                <input type="number" name="nominal" id="nominal" required
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3">
                {{-- Ubah button batal menjadi anchor tag menuju '#' untuk close modal --}}
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