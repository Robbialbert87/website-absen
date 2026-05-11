<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JadwalPegawai;
use Carbon\Carbon;

$today = Carbon::today()->format('Y-m-d');
$month = Carbon::today()->month;
$year = Carbon::today()->year;

$total_jadwal = JadwalPegawai::count();
$cuti_today = JadwalPegawai::where('tanggal_masuk', $today)
    ->whereHas('shift', function($q) {
        $q->where('kategori_jadwal', 'cuti');
    })->count();

$cuti_month = JadwalPegawai::whereMonth('tanggal_masuk', $month)
    ->whereYear('tanggal_masuk', $year)
    ->whereHas('shift', function($q) {
        $q->where('kategori_jadwal', 'cuti');
    })->count();

$samples = JadwalPegawai::with('shift')->latest()->take(10)->get();

echo "Today: $today\n";
echo "Total Jadwal: $total_jadwal\n";
echo "Cuti Today: $cuti_today\n";
echo "Cuti Month: $cuti_month\n";
echo "\nLast 10 Jadwal:\n";
foreach ($samples as $s) {
    echo "Date: {$s->tanggal_masuk}, Shift: " . ($s->shift->kode_shift ?? 'N/A') . ", Kategori: " . ($s->shift->kategori_jadwal ?? 'N/A') . "\n";
}
