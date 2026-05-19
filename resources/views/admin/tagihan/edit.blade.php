<div id="modalEditTagihan" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

    <div class="bg-white w-full max-w-lg p-6 rounded-xl">

        <h2 class="text-xl font-bold mb-4">Edit Data Siswa</h2>

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <input type="text" id="nis" name="nis" class="w-full border p-2 mb-2">
            <input type="text" id="nama" name="nama" class="w-full border p-2 mb-2">

            <select id="kelas" name="kelas" class="w-full border p-2 mb-2">
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>

            <select id="jurusan" name="jurusan" class="w-full border p-2 mb-2">
                <option value="otomotif">OTOMOTIF</option>
                <option value="tkj">TKJ</option>
                <option value="stm">STM</option>
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