<div id="create-siswa"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center invisible opacity-0 scale-95 transition-all duration-200 target:visible target:opacity-100 target:scale-100">

    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg border border-gray-100 relative mx-4">

        {{-- Tombol Silang (Close) --}}
        <a href="#" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-semibold">&times;</a>

        <h2 class="text-xl font-bold text-gray-800 mb-4">Tambah Siswa</h2>

        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required minlength="4" maxlength="10" pattern="[0-9]*" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Contoh: 20260102">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="nama"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas" id="kelas"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="tahun_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                <select name="tahun_masuk" id="tahun_masuk"
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
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="" disabled selected>Pilih Jurusan</option>
                    <option value="otomotif">OTOMOTIF</option>
                    <option value="akuntansi">AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" name="alamat"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">No HP (WhatsApp)</label>
                <input type="text" name="no_hp"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 081234567890" inputmode="tel" pattern="^(08|628)[0-9]{8,12}$"
                    title="Nomor harus dimulai dengan 08 atau 628 dan terdiri dari 10-14 digit angka" required>
                <small class="text-gray-400 italic block mt-1">*Pastikan nomor WhatsApp aktif</small>
            </div>

            <div class="flex justify-end gap-3 mt-5">
                {{-- Ubah button Batal menjadi anchor tag ke '#' untuk menutup target URL --}}
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