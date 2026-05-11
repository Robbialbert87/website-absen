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
        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropForeign(['kepala_user_id']);
            $table->dropColumn('kepala_user_id');
            
            $table->unsignedBigInteger('kepala_pegawai_id')->nullable()->after('keterangan');
            $table->foreign('kepala_pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropForeign(['kepala_pegawai_id']);
            $table->dropColumn('kepala_pegawai_id');
            
            $table->unsignedBigInteger('kepala_user_id')->nullable()->after('keterangan');
            $table->foreign('kepala_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
