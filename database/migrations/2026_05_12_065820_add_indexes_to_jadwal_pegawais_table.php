<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_pegawais', function (Blueprint $table) {
            $table->index(['pegawai_id', 'tanggal_masuk']);
            $table->index(['ruangan_id', 'tanggal_masuk']);
            $table->index('tanggal_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pegawais', function (Blueprint $table) {
            $table->dropIndex(['pegawai_id', 'tanggal_masuk']);
            $table->dropIndex(['ruangan_id', 'tanggal_masuk']);
            $table->dropIndex(['tanggal_masuk']);
        });
    }
};
