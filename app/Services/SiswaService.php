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
            $email = $this->generateEmail($data['nis']);

            // Create user
            $user = User::create([
                'name' => $data['nama'],
                'email' => $email,
                'password' => Hash::make($passwordPlain),
                'role' => 'orang_tua'
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
            $nisLama = $siswa->nis;
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

                // 🔥 PERBAIKAN: Jika NIS berubah, email user di-generate ulang sesuai NIS baru
                if ($nisLama != $data['nis']) {
                    $userData['email'] = $this->generateEmail($data['nis']);
                }

                $siswa->user->update($userData);
            }

            // Cek perubahan nomor HP untuk kirim ulang akun via Queue
            if ($noLama != $noBaru || $nisLama != $data['nis']) {
                $passwordBaru = Str::random(8);

                if ($siswa->user) {
                    $siswa->user->update([
                        'password' => Hash::make($passwordBaru)
                    ]);

                    // Ambil email terbaru yang sudah diupdate
                    $emailAktif = $siswa->user->fresh()->email;

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

    // Tambahkan di dalam class SiswaService

    public function resetAndSendAkun($id)
    {
        return DB::transaction(function () use ($id) {
            $siswa = Siswa::with('user')->findOrFail($id);

            if (!$siswa->user) {
                throw new \Exception('Data User pendukung untuk siswa ini tidak ditemukan.');
            }

            // Generate password baru secara acak
            $passwordBaru = Str::random(8);

            // Update password user ke database
            $siswa->user->update([
                'password' => Hash::make($passwordBaru)
            ]);

            // Picu Job untuk mengirimkan kredensial baru via WhatsApp Fonnte
            KirimAkunSiswaJob::dispatch(
                $siswa->no_hp,
                $siswa->nama,
                $siswa->user->email,
                $passwordBaru
            );

            return $siswa;
        });
    }

    private function generateEmail($nis)
    {
        // Menghilangkan spasi atau karakter aneh jika ada pada NIS
        $nisClean = trim($nis);

        // Format email utama menggunakan NIS (contoh: 24251001@sppsmkutama.com)
        $email = $nisClean . '@sppsmkutama.com';

        // Jika email dengan NIS tersebut belum ada di database, langsung gunakan
        if (!User::where('email', $email)->exists()) {
            return $email;
        }

        // Antisipasi cadangan jika karena suatu alasan NIS double di sistem User (opsional)
        $counter = 1;
        while (User::where('email', $nisClean . '.' . $counter . '@sppsmkutama.com')->exists()) {
            $counter++;
        }

        return $nisClean . '.' . $counter . '@sppsmkutama.com';
    }
}