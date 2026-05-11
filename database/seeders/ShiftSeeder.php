<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            ['kode_shift' => 'P', 'nama_shift' => 'Pagi', 'jam_masuk' => '07:15:00', 'jam_pulang' => '14:15:00', 'warna' => '#3498db'],
            ['kode_shift' => 'J', 'nama_shift' => 'Jumat', 'jam_masuk' => '07:15:00', 'jam_pulang' => '11:00:00', 'warna' => '#2ecc71'],
            ['kode_shift' => 'L', 'nama_shift' => 'Libur', 'jam_masuk' => '00:00:00', 'jam_pulang' => '00:00:00', 'warna' => '#e74c3c'],
        ];

        foreach ($shifts as $s) {
            \App\Models\Shift::create($s);
        }
    }
}
