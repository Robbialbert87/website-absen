<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->enum('kategori_jadwal', ['non_shift', 'shift', 'cuti', 'non_shift_5_hari'])->default('non_shift')->change();
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->enum('kategori_kerja', ['non_shift', 'shift', 'non_shift_5_hari'])->default('non_shift')->change();
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->enum('kategori_jadwal', ['non_shift', 'shift', 'cuti'])->default('non_shift')->change();
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->enum('kategori_kerja', ['non_shift', 'shift'])->default('non_shift')->change();
        });
    }
};
