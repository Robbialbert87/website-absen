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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->nullable()->after('password');
            $table->unsignedBigInteger('ruangan_id')->nullable()->after('pegawai_id');
            
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn(['pegawai_id', 'ruangan_id']);
        });
    }
};
