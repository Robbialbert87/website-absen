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
        Schema::table('pegawai', function (Blueprint $table) {
            $table->enum('kategori_kerja', ['non_shift', 'shift'])->default('non_shift')->after('jabatan');
            $table->foreignId('shift_id')->nullable()->after('kategori_kerja')->constrained('shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn(['kategori_kerja', 'shift_id']);
        });
    }
};
