<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

    <div class="bg-white w-full max-w-lg p-6 rounded-xl">

        <h2 class="text-xl font-bold mb-4">Edit Data Siswa</h2>
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form id="formEditSiswa" method="POST">
            @csrf
            @method('PUT')

            <input type="text" id="nis" name="nis" class="w-full border p-2 mb-2">
            <input type="text" id="nama" name="nama" class="w-full border p-2 mb-2">

            <select id="edit-kelas" name="kelas" class="w-full border p-2 mb-2">
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>

            <div class="mb-3">
                <label for="tahun_masuk" class="block text-sm font-medium text-gray-700">Tahun Masuk</label>
                <select name="tahun_masuk" id="edit-tahun_masuk" class="w-full border rounded p-2">
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

            <select id="edit-jurusan" name="jurusan" class="w-full border p-2 mb-2">
                <option selected default>Pilih Jurusan</option>
                <option value="otomotif">OTOMOTIF</option>
                <option value="akuntansi">AKUNTANSI</option>
            </select>

            <input type="text" id="alamat" name="alamat" class="w-full border p-2 mb-2">
            <input type="text" id="no_hp" name="no_hp" class="w-full border p-2 mb-2">

            <div class="flex justify-end gap-2">
                <button type="button" class="closeModalEdit">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-3 py-1">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>