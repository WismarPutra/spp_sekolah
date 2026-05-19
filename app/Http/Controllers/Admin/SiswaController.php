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
            'nis'     => 'required|unique:siswa',
            'nama'    => 'required',
            'kelas'   => 'required',
            'tahun_masuk'   => 'required',
            'jurusan' => 'required',
            'alamat'  => 'required',
            // Validasi: Harus angka, mulai dengan 08 atau 62, minimal 10 maks 14 digit
            'no_hp'   => ['required', 'regex:/^(08|628)[0-9]{8,12}$/'],
        ], [
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
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $id,
            'nama' => 'required',
            'kelas' => 'required',
            'tahun_masuk' => 'required',
            'jurusan' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required'
        ]);

        $siswa = Siswa::with('user')->findOrFail($id);

        //  SIMPAN NOMOR LAMA
        $noLama = $siswa->no_hp;

        // 🔄 UPDATE DATA
        $siswa->update([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'tahun_masuk' => $request->tahun_masuk,
            'jurusan' => $request->jurusan,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp
        ]);

        // 🔥 CEK APAKAH NOMOR BERUBAH
        if ($noLama != $request->no_hp) {

            // generate password baru
            $passwordBaru = Str::random(8);

            $siswa->user->update([
                'password' => Hash::make($passwordBaru)
            ]);

            // kirim ke nomor baru via queue
            KirimAkunSiswaJob::dispatch(
                $request->no_hp,
                $siswa->nama,
                $siswa->user->email,
                $passwordBaru
            );
        }

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findorfail($id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function resetManual()
    {
        try {
            // 1. Reset status is_sent di tabel Siswa
            \App\Models\Siswa::query()->update(['is_sent' => false]);

            // 2. Opsional: Jika kamu ingin menghapus tagihan bulan berjalan agar tidak double
            // \App\Models\Tagihan::where('bulan', now()->translatedFormat('F'))->delete();

            return back()->with('success', 'Status tagihan berhasil di-reset! Admin kini bisa mengirim ulang tagihan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }
}