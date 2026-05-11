<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'kode_shift' => 'P',
                'nama_shift' => 'Pagi',
                'kategori_jadwal' => 'shift',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '14:00:00',
                'warna' => '#27ae60', // Green
                'keterangan' => 'Jadwal Pagi'
            ],
            [
                'kode_shift' => 'S',
                'nama_shift' => 'Siang',
                'kategori_jadwal' => 'shift',
                'jam_masuk' => '14:00:00',
                'jam_pulang' => '20:00:00',
                'warna' => '#f1c40f', // Yellow
                'keterangan' => 'Jadwal Siang'
            ],
            [
                'kode_shift' => 'M',
                'nama_shift' => 'Malam',
                'kategori_jadwal' => 'shift',
                'jam_masuk' => '20:00:00',
                'jam_pulang' => '08:00:00',
                'warna' => '#2c3e50', // Dark Blue
                'keterangan' => 'Jadwal Malam'
            ],
            [
                'kode_shift' => 'L',
                'nama_shift' => 'Libur',
                'kategori_jadwal' => 'shift',
                'jam_masuk' => '00:00:00',
                'jam_pulang' => '00:00:00',
                'warna' => '#e74c3c', // Red
                'keterangan' => 'Libur'
            ],
        ];

        foreach ($shifts as $shift) {
            \App\Models\Shift::updateOrCreate(
                ['kode_shift' => $shift['kode_shift']],
                $shift
            );
        }
    }
}
