<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangan = [
            ['kode_ruangan' => 'UGD', 'nama_ruangan' => 'Unit Gawat Darurat', 'keterangan' => 'Ruang Gawat Darurat'],
            ['kode_ruangan' => 'POLI', 'nama_ruangan' => 'Poliklinik', 'keterangan' => 'Ruang Rawat Jalan'],
            ['kode_ruangan' => 'LAB', 'nama_ruangan' => 'Laboratorium', 'keterangan' => 'Ruang Laboratorium'],
            ['kode_ruangan' => 'FARMASI', 'nama_ruangan' => 'Farmasi', 'keterangan' => 'Ruang Obat'],
        ];

        foreach ($ruangan as $r) {
            \App\Models\Ruangan::create($r);
        }
    }
}
