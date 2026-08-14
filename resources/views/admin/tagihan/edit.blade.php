{{-- MODAL EDIT MURNI HTML5 & CSS (:target) --}}
<div id="modal-edit-{{ $item->id }}"
    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none target:opacity-100 target:pointer-events-auto flex items-center justify-center z-50 transition-opacity duration-200 text-left">

    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 relative mx-4">
        <a href="#" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</a>

        {{-- JUDUL MODAL --}}
        <h2 class="text-base font-bold mb-1 text-gray-800">Edit Data Tagihan</h2>
        <p class="text-xs text-gray-400 mb-4">Siswa: <span
                class="font-semibold text-gray-600">{{ $item->siswa->nama }}</span></p>

        {{-- FORM UPDATE --}}
        <form action="{{ route('tagihan.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- EDIT PERIODE BULAN & TAHUN --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label for="bulan-{{ $item->id }}"
                        class="block text-xs font-bold text-gray-400 uppercase mb-1">Bulan</label>
                    <select name="bulan" id="bulan-{{ $item->id }}" required
                        class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                        @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                            {{ $item->bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                            @endfor
                    </select>
                </div>
                <div>
                    <label for="tahun-{{ $item->id }}"
                        class="block text-xs font-bold text-gray-400 uppercase mb-1">Tahun</label>
                    <select name="tahun" id="tahun-{{ $item->id }}" required
                        class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                        @php $tahunSekarang = date('Y'); @endphp
                        @for ($i = $tahunSekarang; $i >= $tahunSekarang - 3; $i--)
                        <option value="{{ $i }}" {{ $item->tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- INPUT NOMINAL / JUMLAH TAGIHAN --}}
            <div class="mb-4">
                <label for="jumlah-{{ $item->id }}" class="block text-xs font-bold text-gray-400 uppercase mb-1">Nominal
                    Tagihan (Rp)</label>
                <input type="number" name="jumlah" id="jumlah-{{ $item->id }}" value="{{ $item->jumlah }}" required
                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition font-medium text-gray-800">
            </div>

            {{-- STATUS PEMBAYARAN (BISA DIEDIT UNTUK KONDISI DARURAT) --}}
            <div class="mb-5">
                <label for="status-{{ $item->id }}" class="block text-xs font-bold text-gray-400 uppercase mb-1">Status
                    Pembayaran</label>
                <select name="status" id="status-{{ $item->id }}" required
                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-gray-800">
                    <option value="belum" {{ $item->status == 'belum' ? 'selected' : '' }}>BELUM LUNAS</option>
                    <option value="lunas" {{ $item->status == 'lunas' ? 'selected' : '' }}>LUNAS (MANUAL / PAKASIR)
                        GANGGUAN)</option>
                </select>
                <p class="text-[11px] text-amber-600 mt-1">*Ubah ke LUNAS hanya jika siswa sudah membayar sah melalui
                    loket/bank namun Pakasir mengalami kendala callback.</p>
            </div>

            {{-- AKSI BUTTONS --}}
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <a href="#"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>