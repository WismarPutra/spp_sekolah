<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

    <div class="bg-white p-6 rounded w-96">

        <h2 class="text-lg font-bold mb-4">Form SPP</h2>

        <form action="{{ route('spp.store')}}" id="formSpp" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method">

            <div class="mb-3">
                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" id="tahun" class="w-full border rounded p-2">
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
                <label>Jurusan</label>
                <select name="jurusan" id="jurusan" class="w-full border px-2 py-1 rounded">
                    <option selected default>Jurusan</option>
                    <option value="otomotif">OTOMOTIF</option>
                    <option value="akuntansi">AKUNTANSI</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Nominal</label>
                <input type="number" name="nominal" id="nominal" class="w-full border px-2 py-1 rounded">
            </div>

            <div class="mb-3">
                <label>Kelas</label>
                <select name="kelas" id="kelas" class="w-full border px-2 py-1 rounded">
                    @for ($i = 10; $i <= 12; $i++) <option value="{{ $i }}">
                        {{ $i }}
                        </option>
                        @endfor
                </select>
            </div>



            <div class="flex justify-end gap-2">
                <button type="button" id="closeModal" class="bg-gray-400 px-3 py-1 rounded closeModal">
                    Batal
                </button>

                <button class="bg-blue-500 text-white px-3 py-1 rounded">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>