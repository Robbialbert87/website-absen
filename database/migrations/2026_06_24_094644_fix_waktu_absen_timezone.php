<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("UPDATE absensi_kegiatan SET waktu_absen = datetime(waktu_absen, '+7 hours')");
        } else {
            DB::statement("UPDATE absensi_kegiatan SET waktu_absen = DATE_ADD(waktu_absen, INTERVAL 7 HOUR)");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("UPDATE absensi_kegiatan SET waktu_absen = datetime(waktu_absen, '-7 hours')");
        } else {
            DB::statement("UPDATE absensi_kegiatan SET waktu_absen = DATE_SUB(waktu_absen, INTERVAL 7 HOUR)");
        }
    }
};
