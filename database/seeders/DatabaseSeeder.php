<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Kecamatan ────────────────────────────────────────────────────────
        $kecamatanData = [
            'Polewali', 'Binuang', 'Anreapi', 'Campalagian', 'Tinambung',
            'Mapilli', 'Tapango', 'Wonomulyo', 'Matakali', 'Bulo',
            'Tubbi Taramanu', 'Limboro', 'Alu', 'Luyo', 'Balanipa',
            'Sendana',
        ];

        $kecamatans = [];
        foreach ($kecamatanData as $nama) {
            $kecamatans[$nama] = Kecamatan::create(['nama' => $nama]);
        }

        // ─── Desa sample ──────────────────────────────────────────────────────
        $desaSample = [
            'Polewali' => ['Polewali', 'Darma', 'Takatidung', 'Manding', 'Pekkabata'],
            'Wonomulyo' => ['Wonomulyo', 'Sugihwaras', 'Sidorejo', 'Kebunsari', 'Arjowinangun'],
            'Campalagian' => ['Campalagian', 'Pussui', 'Baru', 'Katumbangan', 'Lapeo'],
        ];

        foreach ($desaSample as $kecNama => $desas) {
            foreach ($desas as $desaNama) {
                Desa::create([
                    'kecamatan_id' => $kecamatans[$kecNama]->id,
                    'nama' => $desaNama,
                ]);
            }
        }

        // Desa sederhana untuk kecamatan lainnya
        foreach ($kecamatans as $nama => $kec) {
            if (! isset($desaSample[$nama])) {
                Desa::create(['kecamatan_id' => $kec->id, 'nama' => 'Desa '.$nama.' 1']);
                Desa::create(['kecamatan_id' => $kec->id, 'nama' => 'Desa '.$nama.' 2']);
            }
        }

        // ─── Admin ────────────────────────────────────────────────────────────
        User::create([
            'name' => 'Admin Dinas',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // ─── Pimpinan ─────────────────────────────────────────────────────────
        User::create([
            'name' => 'Kepala Dinas',
            'email' => 'pimpinan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'is_active' => true,
        ]);

        // ─── Petugas kecamatan (Semua Kecamatan) ──────────────────────────────
        $petugasUsers = [];
        foreach ($kecamatans as $namaKec => $kec) {
            $email = 'petugas.' . strtolower(str_replace(' ', '', $namaKec)) . '@gmail.com';
            $petugasUsers[] = User::create([
                'name' => 'Petugas ' . $namaKec,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'kecamatan_id' => $kec->id,
                'is_active' => true,
            ]);
        }

        // ─── Data Percobaan Laporan Panen ─────────────────────────────────────
        $varietas = ['Ciherang', 'IR64', 'Inpari 32', 'Mekongga', 'Cisantana'];
        $musim = ['Musim Hujan', 'Musim Kemarau'];
        $status = ['draft', 'menunggu_validasi', 'disetujui', 'ditolak'];

        // Ambil admin untuk validasi
        $admin = User::where('role', 'admin')->first();

        foreach ($petugasUsers as $petugas) {
            $desas = Desa::where('kecamatan_id', $petugas->kecamatan_id)->get();
            
            foreach ($desas as $desa) {
                // Buat 1-3 laporan per desa
                $jmlLaporan = rand(1, 3);
                for ($i = 0; $i < $jmlLaporan; $i++) {
                    $luasTanam = rand(10, 50) + (rand(0, 9) / 10);
                    $luasPanen = $luasTanam - (rand(0, 2) + (rand(0, 9) / 10));
                    if ($luasPanen <= 0) $luasPanen = $luasTanam;
                    
                    // Produksi sekitar 5-8 ton per hektar
                    $produksi = $luasPanen * (rand(50, 80) / 10);

                    $sts = $status[array_rand($status)];
                    
                    \App\Models\LaporanPanen::create([
                        'user_id' => $petugas->id,
                        'kecamatan_id' => $petugas->kecamatan_id,
                        'desa_id' => $desa->id,
                        'musim_tanam' => $musim[array_rand($musim)],
                        'tahun' => date('Y'),
                        'luas_tanam' => $luasTanam,
                        'luas_panen' => $luasPanen,
                        'produksi' => $produksi,
                        'varietas_padi' => $varietas[array_rand($varietas)],
                        'keterangan' => 'Laporan percobaan untuk ' . $desa->nama,
                        'status' => $sts,
                        'catatan_penolakan' => $sts === 'ditolak' ? 'Ada anomali data luasan panen' : null,
                        'validated_by' => ($sts === 'disetujui' || $sts === 'ditolak') && $admin ? $admin->id : null,
                        'validated_at' => ($sts === 'disetujui' || $sts === 'ditolak') ? now() : null,
                    ]);
                }
            }
        }
    }
}
