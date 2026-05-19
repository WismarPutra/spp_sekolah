<!-- MODAL -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg">

        <h2 class="text-xl font-bold mb-4">Tambah Siswa</h2>

        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>NIS</label>
                <input type="text" name="nis" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label for="kelas" class="block mb-2.5 text-sm font-medium text-heading">Kelas</label>
                <select name="kelas" id="kelas"
                    class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                    required>
                    <option selected default>Pilih Kelas</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tahun_masuk" class="block text-sm font-medium text-gray-700">Tahun Masuk</label>
                <select name="tahun_masuk" id="tahun_masuk" class="w-full border rounded p-2">
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

            <div class="mb-3">
                <label for="jurusan" class="block mb-2.5 text-sm font-medium text-heading">Jurusan</label>
                <select name="jurusan" id="jurusan"
                    class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                    required>
                    <option selected default>Pilih Jurusan</option>
                    <option value="otomotif">OTOMOTIF</option>
                    <option value="akuntansi">AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <input type="text" name="alamat" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label>No HP (WhatsApp)</label>
                <input type="text" name="no_hp" class="w-full border p-2 rounded" placeholder="Contoh: 081234567890"
                    inputmode="tel" pattern="^(08|628)[0-9]{8,12}$"
                    title="Nomor harus dimulai dengan 08 atau 628 dan terdiri dari 10-14 digit angka" required>
                <small class="text-gray-500 italic">*Pastikan nomor WhatsApp aktif</small>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="bg-gray-400 px-4 py-2 rounded closeModalSiswa">
                    Batal
                </button>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>
            </div>

        </form>

    </div>

</div>