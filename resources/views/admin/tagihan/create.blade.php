<div id="modal-tambah-tagihan"
    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none target:opacity-100 target:pointer-events-auto flex items-center justify-center z-50 transition-opacity duration-200">

    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative mx-4">
        <a href="#" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</a>

        <h2 class="text-lg font-semibold mb-4 text-gray-800">Tambah Tagihan Baru</h2>

        <form action="{{ route('tagihan.store') }}" method="POST" class="flex flex-col">
            @csrf

            {{-- LOGIKA SELEKTOR FORM MURNI HTML & TAILWIND --}}
            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100 order-1">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Tipe Pembuatan</span>

                {{-- 
                    Trik CSS Sibling: Kita letakkan input radio di luar label agar menjadi sibling dari container bawah.
                    Kita sembunyikan input aslinya secara visual menggunakan kelas Tailwind (sr-only atau opacity-0 posisi absolut), 
                    lalu kita buat 'custom radio button' menggunakan pseudo-element atau peer utilitas.
                --}}
                <div class="flex gap-4 relative">
                    <input type="radio" id="tipe-individu" name="tipe_tagihan" value="individu" checked
                        class="peer/individu sr-only">
                    <label for="tipe-individu"
                        class="flex items-center gap-2 text-sm text-gray-700 font-medium cursor-pointer peer-checked/individu:text-blue-600">
                        <span
                            class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center peer-checked/individu:border-blue-600 bg-white">
                            <span class="w-2 h-2 rounded-full bg-transparent peer-checked/individu:bg-blue-600"></span>
                        </span>
                        Per Siswa (Individu)
                    </label>

                    <input type="radio" id="tipe-massal" name="tipe_tagihan" value="massal" class="peer/massal sr-only">
                    <label for="tipe-massal"
                        class="flex items-center gap-2 text-sm text-gray-700 font-medium cursor-pointer peer-checked/massal:text-blue-600">
                        <span
                            class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center peer-checked/massal:border-blue-600 bg-white">
                            <span class="w-2 h-2 rounded-full bg-transparent peer-checked/massal:bg-blue-600"></span>
                        </span>
                        Massal (Semua Siswa)
                    </label>

                    {{-- 
                        KONTROL DROPDOWN VIA CSS SIBLING 
                        Karena pembungkus form menggunakan flex flex-col, kita bisa mengatur posisi urutan visual menggunakan utility `order-*` 
                        sehingga penulisan elemen dropdown ini diletakkan tepat di bawah input radio secara hirarki CSS (agar peer berfungsi), 
                        namun secara visual tetap rapi berurutan ke bawah.
                    --}}

                    {{-- CONTAINER FORM INDIVIDU (PILIH SISWA) --}}
                    <div class="absolute top-12 left-0 w-full bg-white space-y-3 peer-checked/massal:hidden pt-2 z-10">
                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Pilih Siswa & Parameter
                                SPP</label>
                            <select name="siswa_id"
                                class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="" disabled selected>-- Pilih Siswa --</option>
                                @foreach($siswa as $s)
                                @php
                                $sppSiswa = $spp->where('kelas', $s->kelas)->where('jurusan', $s->jurusan)->first();
                                $sppId = $sppSiswa ? $sppSiswa->id : '';
                                @endphp
                                <option value="{{ $s->id }}">
                                    {{ $s->nama }} — Kelas {{ $s->kelas }} ({{ strtoupper($s->jurusan) }})
                                </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">*Sistem otomatis mencocokkan nominal SPP
                                berdasarkan data master penempatan kelas & jurusan siswa.</p>
                        </div>

                        {{-- CONTAINER PARAMETER TARIF ACUAN --}}
                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Gunakan Parameter Tarif SPP
                                Acuan</label>
                            <select name="spp_id"
                                class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="" disabled selected>-- Pilih Referensi Angkatan / Jurusan --</option>
                                @foreach($spp as $sp)
                                <option value="{{ $sp->id }}">
                                    Angkatan {{ $sp->tahun }} — {{$sp->kelas}} - {{ strtoupper($sp->jurusan) }} — (Rp
                                    {{ number_format($sp->nominal) }})
                                </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-red-400 mt-1">*Pilih opsi ini jika menginput tagihan bertipe
                                khusus/individu.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Jarak spacer pengganti agar layout tidak tabrakan karena posisi absolute di atas --}}
            <div class="peer-checked/massal:hidden h-[175px] order-2"></div>

            {{-- PERIODE BULAN & TAHUN (Tetap tampil di kedua kondisi) --}}
            <div class="grid grid-cols-2 gap-3 mb-4 order-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Bulan</label>
                    <select name="bulan"
                        class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                            {{ $i == \Carbon\Carbon::now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                            @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Tahun</label>
                    <select name="tahun"
                        class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @php $tahunSekarang = date('Y'); @endphp
                        @for ($i = $tahunSekarang; $i >= $tahunSekarang - 3; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 order-4">
                <a href="#"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                    Simpan & Terbitkan
                </button>
            </div>
        </form>
    </div>
</div>