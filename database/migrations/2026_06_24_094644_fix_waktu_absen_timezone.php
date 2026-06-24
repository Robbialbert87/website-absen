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
        DB::statement("UPDATE absensi_kegiatan SET waktu_absen = DATE_ADD(waktu_absen, INTERVAL 7 HOUR)");
    }

    public function down(): void
    {
        DB::statement("UPDATE absensi_kegiatan SET waktu_absen = DATE_SUB(waktu_absen, INTERVAL 7 HOUR)");
    }
};
