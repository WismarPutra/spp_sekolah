<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Services\SiswaService;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Jobs\KirimAkunSiswaJob;

class SiswaController extends Controller

{
    protected $siswaService;

    public function __construct(SiswaService $siswaService)
    {
        $this->siswaService = $siswaService;
    }

    public function index()
    {
        $siswas = Siswa::latest()->get();
        $urutanSiswa = Siswa::orderBy('nis', 'asc')->get();
        return view('admin.siswa.index', compact(
            'siswas',
            'urutanSiswa'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis'         => 'required|numeric|digits_between:4,10|unique:siswa', // Tambahkan ini
            'nama'        => 'required',
            'kelas'       => 'required',
            'tahun_masuk' => 'required',
            'jurusan'     => 'required',
            'alamat'      => 'required',
            'no_hp'       => ['required', 'regex:/^(08|628)[0-9]{8,12}$/'],
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.digits_between' => 'NIS harus berjumlah antara 4 sampai 10 digit.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08... atau 628...',
        ]);

        $this->siswaService->create($request->all());
        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Data Siswa Berdasarkan ID
        $dataSiswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact(
            'dataSiswa'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Tetap lakukan validasi di Controller sebelum masuk ke Service Layer
        $request->validate([
            'nis'         => 'required|numeric|digits_between:4,10|unique:siswa,nis,' . $id,
            'nama'        => 'required',
            'kelas'       => 'required', // Memakai 'kelas' pasca perbaikan name HTML
            'tahun_masuk' => 'required',
            'jurusan'     => 'required',
            'alamat'      => 'required',
            'no_hp'       => ['required', 'regex:/^(08|628)[0-9]{8,12}$/']
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.digits_between' => 'NIS harus berjumlah antara 4 sampai 10 digit.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08... atau 628...',
        ]);

        // Kirim data bersih ke Service Layer
        $this->siswaService->update($id, $request->all());

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Panggil fungsi delete dari service
        $this->siswaService->delete($id);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function resetManual()
    {
        try {
            // 1. Reset status counter is_sent di tabel Siswa kembali ke angka 0
            \App\Models\Siswa::query()->update(['is_sent' => 0]);

            // 2. Opsional: Hapus tagihan bulan berjalan (menggunakan angka) jika ingin buat ulang dari awal
            $bulanAngkaIni = (int)now()->format('n');
            $tahunIni = now()->format('Y');

            // Silakan hapus tanda komentar (//) di bawah jika ingin tagihan lama di bulan ini terhapus saat di-reset
            // \App\Models\Tagihan::where('bulan', $bulanAngkaIni)->where('tahun', $tahunIni)->where('status', 'belum')->delete();

            return back()->with('success', 'Status tagihan berhasil di-reset! Seluruh status kirim siswa kembali kosong dan siap di-generate ulang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }
}