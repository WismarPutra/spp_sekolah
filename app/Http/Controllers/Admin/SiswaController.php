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
        return view('admin.siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis'         => 'required|numeric|digits_between:8,10|unique:siswa', // Tambahkan ini
            'nama'        => 'required',
            'kelas'       => 'required',
            'tahun_masuk' => 'required',
            'jurusan'     => 'required',
            'alamat'      => 'required',
            'no_hp'       => ['required', 'regex:/^(08|628)[0-9]{8,12}$/'],
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.digits_between' => 'NIS harus berjumlah antara 8 sampai 10 digit.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'no_hp.required' => 'Nomor HP (WhatsApp) wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08... atau 628...',
        ]);

        $this->siswaService->create($request->all());
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
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
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact(
            'siswa'
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
            'kelas'       => 'required', 
            'tahun_masuk' => 'required',
            'jurusan'     => 'required',
            'alamat'      => 'required',
            'no_hp'       => ['required', 'regex:/^(08|628)[0-9]{8,12}$/']
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.digits_between' => 'NIS harus berjumlah antara 4 sampai 10 digit.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'no_hp.required' => 'Nomor HP (WhatsApp) wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08... atau 628...',
        ]);

        // Kirim data bersih ke Service Layer
        $this->siswaService->update($id, $request->all());

        return redirect()->route('siswa.index')->with('success', 'Data berhasil diupdate');
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

    // Tambahkan di dalam class SiswaController

    public function kirimUlangAkun($id)
    {
        try {
            $this->siswaService->resetAndSendAkun($id);
            return back()->with('success', 'Password berhasil di-reset dan informasi akun baru telah dikirimkan ke WhatsApp orang tua.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim ulang akun: ' . $e->getMessage());
        }
    }
}