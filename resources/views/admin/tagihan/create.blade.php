<div id="modal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition duration-200">

    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">

        <!-- Header -->
        <h2 class="text-lg font-semibold mb-4">Tambah Tagihan</h2>

        <!-- Close -->
        <button id="closeModal" class="absolute top-2 right-3 text-gray-500 text-xl">
            &times;
        </button>

        <!-- Form -->
        <form action="{{ route('tagihan.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Tipe Pembuatan</label><br>
                <input type="radio" name="tipe_tagihan" value="individu" onclick="toggleTipe('individu')" checked> Per
                Siswa
                <input type="radio" name="tipe_tagihan" value="massal" onclick="toggleTipe('massal')"> Masal (Semua
                Siswa)
            </div>
            <!-- Siswa -->
            <div id="section_siswa" class="mb-3">
                <label class="block text-sm font-medium">Siswa</label>
                <select name="siswa_id" id="siswa_select" class="w-full border rounded p-2">
                    <option value="" disabled selected>-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                    @php
                    // Cari data SPP yang cocok dengan kelas dan jurusan siswa ini
                    $sppSiswa = $spp->where('kelas', $s->kelas)->where('jurusan', $s->jurusan)->first();
                    $nominal = $sppSiswa ? $sppSiswa->nominal : 0;
                    $sppId = $sppSiswa ? $sppSiswa->id : '';
                    @endphp
                    <option value="{{ $s->id }}" data-nominal="{{ $nominal }}" data-sppid="{{ $sppId }}">
                        {{ $s->nama }} - {{ $s->kelas }} - {{ strtoupper($s->jurusan) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- SPP -->
            <div id="section_spp" class=" mb-3">
                <label class="block text-sm">SPP</label>
                <select name="spp_id" id="spp_id_input" class="w-full border rounded p-2">
                    <option value="" disabled selected>-- Pilih SPP --</option>
                    @foreach($spp as $sp)
                    <option value="{{ $sp->id }}">
                        {{ $sp->tahun }} - {{$sp->jurusan}} - Rp {{ number_format($sp->nominal) }}
                    </option>
                    @endforeach
                </select>

                <!-- Jumlah -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nominal</label>
                    <input type="number" name="jumlah" id="nominal_input" class="w-full border rounded p-2 bg-gray-100"
                        readonly>
                </div>
            </div>



            <!-- Bulan -->
            <div class="mb-3">
                <label class="block text-sm">Bulan</label>
                <select name="bulan" id="" class="w-full border rounded p-2">
                    @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                        {{ $i == \Carbon\Carbon::now()->month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                        @endfor

                </select>

            </div>

            <!-- Tahun -->
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

            <!-- Button -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded closeModalTagihan">
                    Batal
                </button>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>