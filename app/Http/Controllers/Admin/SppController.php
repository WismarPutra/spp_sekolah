<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use Illuminate\Http\Request;


class SppController extends Controller
{
    public function index()
    {
        $spp = Spp::all();
        return view('admin.spp.index', compact('spp'));
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

        return redirect()->back()->with('success', 'Data SPP berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $spp = Spp::findOrFail($id);
        $spp->update($request->all());

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $spp = Spp::findOrFail($id);
        $spp->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}