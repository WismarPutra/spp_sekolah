<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Tagihan;
use Illuminate\Http\Request;


class SppController extends Controller
{
    public function index()
    {
        $spp = Spp::orderBy('jurusan', 'asc')->orderBy('kelas', 'asc')->get();
        return view('admin.spp.index', compact('spp'));
    }

    public function create()
    {
        return view('admin.spp.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'jurusan' => 'required',
            'kelas' => 'required',
            'nominal' => 'required|numeric', // Pastikan divalidasi
        ]);

        // Cek apakah kombinasi tahun, kelas, dan jurusan sudah ada
        $exists = Spp::where('tahun', $request->tahun)
            ->where('kelas', $request->kelas)
            ->where('jurusan', $request->jurusan)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput() // Agar admin tidak perlu ngetik ulang yang lain
                ->with('error', 'Data SPP untuk tahun, kelas, dan jurusan tersebut sudah ada!');
        }

        Spp::create($request->all()); // Ini akan mengambil 'nominal' dari form

        return redirect()->route('spp.index')->with('success', 'Data SPP berhasil disimpan');
    }

    public function edit($id)
    {
        $spp = Spp::findOrFail($id);
        return view('admin.spp.edit', compact('spp'));
    }

    public function update(Request $request, $id)
    {
        $spp = Spp::findOrFail($id);

        // Cek apakah kombinasi tahun, kelas, dan jurusan sudah ada, kecuali untuk data ini sendiri
        $exists = Spp::where('tahun', $request->tahun)
            ->where('kelas', $request->kelas)
            ->where('jurusan', $request->jurusan)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput() // Agar admin tidak perlu ngetik ulang yang lain
                ->with('error', 'Data SPP untuk tahun, kelas, dan jurusan tersebut sudah ada!');
        }
        
        $spp->update($request->all());

        return redirect()->route('spp.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        // Cek apakah SPP masih digunakan di tabel siswa
        $isUsed = Siswa::where('spp_id', $id)->exists();
        if ($isUsed) {
            return redirect()->back()->with('error', 'Data SPP gagal dihapus karena masih digunakan pada data Tagihan siswa!');
        }

        $spp = Spp::findOrFail($id);
        $spp->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}