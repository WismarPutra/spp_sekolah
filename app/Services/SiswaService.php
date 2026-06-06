<?php

namespace App\Services;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\KirimAkunSiswaJob;

class SiswaService
{
    protected $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {

            // Format nomor
            $no_hp = preg_replace('/^62/', '0', $data['no_hp']);

            // Generate password
            $passwordPlain = Str::random(8);

            // Generate email
            $email = $this->generateEmail($data['nama']);

            // Create user
            $user = User::create([
                'name' => $data['nama'],
                'email' => $email,
                'password' => Hash::make($passwordPlain),
                'role' => 'user'
            ]);

            // Create siswa
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nis' => $data['nis'],
                'nama' => $data['nama'],
                'kelas' => $data['kelas'],
                'jurusan' => $data['jurusan'],
                'alamat' => $data['alamat'],
                'tahun_masuk' => $data['tahun_masuk'],
                'no_hp' => $no_hp,
            ]);

            // Kirim WA
            KirimAkunSiswaJob::dispatch(
                $no_hp,
                $data['nama'],
                $email,
                $passwordPlain
            );

            return $siswa;
        });
    }

    // 🔥 METHOD UPDATE BARU
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $siswa = Siswa::with('user')->findOrFail($id);

            $noLama = $siswa->no_hp;
            // Standardisasi nomor baru seperti saat create (62 -> 0)
            $noBaru = preg_replace('/^62/', '0', $data['no_hp']);

            // Update data Siswa
            $siswa->update([
                'nis'         => $data['nis'],
                'nama'        => $data['nama'],
                'kelas'       => $data['kelas'],
                'tahun_masuk' => $data['tahun_masuk'],
                'jurusan'     => $data['jurusan'],
                'alamat'      => $data['alamat'],
                'no_hp'       => $noBaru
            ]);

            // Update nama di User jika relasi ada
            if ($siswa->user) {
                $siswa->user->update([
                    'name' => $data['nama']
                ]);
            }

            // Cek perubahan nomor HP untuk kirim ulang akun via Queue
            if ($noLama != $noBaru) {
                $passwordBaru = Str::random(8);

                if ($siswa->user) {
                    $siswa->user->update([
                        'password' => Hash::make($passwordBaru)
                    ]);

                    KirimAkunSiswaJob::dispatch(
                        $noBaru,
                        $siswa->nama,
                        $siswa->user->email,
                        $passwordBaru
                    );
                }
            }

            return $siswa;
        });
    }

    // 🔥 METHOD DELETE BARU
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $siswa = Siswa::findOrFail($id);

            // Hapus User pendukung terlebih dahulu agar tidak menjadi data yatim
            if ($siswa->user) {
                $siswa->user->delete();
            }

            return $siswa->delete();
        });
    }

    private function generateEmail($nama)
    {
        $namaDepan = Str::slug($nama, '.');

        $parts = explode(' ', strtolower($nama));
        $namaBelakang = implode('.', array_reverse($parts));

        $email1 = $namaDepan . '@sppsmkutama.com';
        $email2 = $namaBelakang . '@sppsmkutama.com';

        if (!User::where('email', $email1)->exists()) {
            return $email1;
        }

        if (!User::where('email', $email2)->exists()) {
            return $email2;
        }

        $counter = 1;
        while (User::where('email', $namaDepan . $counter . '@sppsmkutama.com')->exists()) {
            $counter++;
        }

        return $namaDepan . $counter . '@sppsmkutama.com';
    }
}