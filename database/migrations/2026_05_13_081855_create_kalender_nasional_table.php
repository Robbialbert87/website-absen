<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_nasional', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('nama_hari_libur');
            $table->enum('jenis', ['nasional', 'cuti_bersama'])->default('nasional');
            $table->string('warna', 20)->default('#dc3545');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->unique('tanggal'); // one holiday per date
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_nasional');
    }
};
