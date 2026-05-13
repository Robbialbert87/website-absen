<?php

namespace Database\Seeders;

use App\Models\KalenderNasional;
use Illuminate\Database\Seeder;

class KalenderNasionalSeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // ===================== 2025 =====================
            // Nasional
            ['tanggal' => '2025-01-01', 'nama_hari_libur' => 'Tahun Baru Masehi',             'jenis' => 'nasional'],
            ['tanggal' => '2025-01-27', 'nama_hari_libur' => 'Isra Miraj Nabi Muhammad SAW',   'jenis' => 'nasional'],
            ['tanggal' => '2025-01-29', 'nama_hari_libur' => 'Tahun Baru Imlek',               'jenis' => 'nasional'],
            ['tanggal' => '2025-03-29', 'nama_hari_libur' => 'Hari Raya Nyepi',                'jenis' => 'nasional'],
            ['tanggal' => '2025-03-31', 'nama_hari_libur' => 'Idul Fitri 1446 H',              'jenis' => 'nasional'],
            ['tanggal' => '2025-04-01', 'nama_hari_libur' => 'Idul Fitri 1446 H (Hari Kedua)', 'jenis' => 'nasional'],
            ['tanggal' => '2025-04-18', 'nama_hari_libur' => 'Wafat Yesus Kristus',            'jenis' => 'nasional'],
            ['tanggal' => '2025-04-20', 'nama_hari_libur' => 'Kebangkitan Yesus Kristus',      'jenis' => 'nasional'],
            ['tanggal' => '2025-05-01', 'nama_hari_libur' => 'Hari Buruh Internasional',       'jenis' => 'nasional'],
            ['tanggal' => '2025-05-12', 'nama_hari_libur' => 'Hari Raya Waisak',               'jenis' => 'nasional'],
            ['tanggal' => '2025-05-29', 'nama_hari_libur' => 'Kenaikan Yesus Kristus',         'jenis' => 'nasional'],
            ['tanggal' => '2025-06-01', 'nama_hari_libur' => 'Hari Lahir Pancasila',           'jenis' => 'nasional'],
            ['tanggal' => '2025-06-06', 'nama_hari_libur' => 'Idul Adha 1446 H',              'jenis' => 'nasional'],
            ['tanggal' => '2025-06-27', 'nama_hari_libur' => 'Tahun Baru Islam 1447 H',        'jenis' => 'nasional'],
            ['tanggal' => '2025-08-17', 'nama_hari_libur' => 'Hari Kemerdekaan RI',            'jenis' => 'nasional'],
            ['tanggal' => '2025-09-05', 'nama_hari_libur' => 'Maulid Nabi Muhammad SAW',       'jenis' => 'nasional'],
            ['tanggal' => '2025-12-25', 'nama_hari_libur' => 'Hari Raya Natal',                'jenis' => 'nasional'],
            // Cuti Bersama 2025
            ['tanggal' => '2025-01-28', 'nama_hari_libur' => 'Cuti Bersama Tahun Baru Imlek', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-03-28', 'nama_hari_libur' => 'Cuti Bersama Hari Raya Nyepi',  'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-04-02', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',        'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-04-03', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',        'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-04-04', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',        'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-04-07', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',        'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-05-13', 'nama_hari_libur' => 'Cuti Bersama Hari Raya Waisak', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2025-12-26', 'nama_hari_libur' => 'Cuti Bersama Hari Natal',        'jenis' => 'cuti_bersama'],

            // ===================== 2026 =====================
            // Nasional
            ['tanggal' => '2026-01-01', 'nama_hari_libur' => 'Tahun Baru Masehi',             'jenis' => 'nasional'],
            ['tanggal' => '2026-01-17', 'nama_hari_libur' => 'Isra Miraj Nabi Muhammad SAW',  'jenis' => 'nasional'],
            ['tanggal' => '2026-02-17', 'nama_hari_libur' => 'Tahun Baru Imlek',              'jenis' => 'nasional'],
            ['tanggal' => '2026-03-19', 'nama_hari_libur' => 'Hari Raya Nyepi',               'jenis' => 'nasional'],
            ['tanggal' => '2026-03-20', 'nama_hari_libur' => 'Idul Fitri 1447 H',             'jenis' => 'nasional'],
            ['tanggal' => '2026-03-21', 'nama_hari_libur' => 'Idul Fitri 1447 H (Hari Kedua)','jenis' => 'nasional'],
            ['tanggal' => '2026-04-03', 'nama_hari_libur' => 'Wafat Yesus Kristus',           'jenis' => 'nasional'],
            ['tanggal' => '2026-05-01', 'nama_hari_libur' => 'Hari Buruh Internasional',      'jenis' => 'nasional'],
            ['tanggal' => '2026-05-14', 'nama_hari_libur' => 'Kenaikan Yesus Kristus',        'jenis' => 'nasional'],
            ['tanggal' => '2026-05-27', 'nama_hari_libur' => 'Hari Raya Waisak',              'jenis' => 'nasional'],
            ['tanggal' => '2026-06-01', 'nama_hari_libur' => 'Hari Lahir Pancasila',          'jenis' => 'nasional'],
            ['tanggal' => '2026-05-26', 'nama_hari_libur' => 'Idul Adha 1447 H',              'jenis' => 'nasional'],
            ['tanggal' => '2026-06-16', 'nama_hari_libur' => 'Tahun Baru Islam 1448 H',       'jenis' => 'nasional'],
            ['tanggal' => '2026-08-17', 'nama_hari_libur' => 'Hari Kemerdekaan RI',           'jenis' => 'nasional'],
            ['tanggal' => '2026-08-25', 'nama_hari_libur' => 'Maulid Nabi Muhammad SAW',      'jenis' => 'nasional'],
            ['tanggal' => '2026-12-25', 'nama_hari_libur' => 'Hari Raya Natal',               'jenis' => 'nasional'],
            // Cuti Bersama 2026 (estimasi)
            ['tanggal' => '2026-01-02', 'nama_hari_libur' => 'Cuti Bersama Tahun Baru',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-16', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-17', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-18', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-23', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-24', 'nama_hari_libur' => 'Cuti Bersama Idul Fitri',       'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-12-24', 'nama_hari_libur' => 'Cuti Bersama Natal',            'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-12-26', 'nama_hari_libur' => 'Cuti Bersama Natal',            'jenis' => 'cuti_bersama'],
        ];

        foreach ($holidays as $h) {
            KalenderNasional::updateOrCreate(
                ['tanggal' => $h['tanggal']],
                [
                    'nama_hari_libur' => $h['nama_hari_libur'],
                    'jenis'           => $h['jenis'],
                    'warna'           => $h['jenis'] === 'cuti_bersama' ? '#ffc107' : '#e74c3c',
                    'status_aktif'    => true,
                ]
            );
        }
    }
}
