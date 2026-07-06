<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kegiatan')) {
            return;
        }

        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('lokasi');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->enum('tipe', ['apel', 'kegiatan', 'kegiatan_langsung'])->default('kegiatan')->after('status');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
