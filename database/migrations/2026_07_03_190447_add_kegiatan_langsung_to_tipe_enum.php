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
        DB::statement("ALTER TABLE kegiatan MODIFY COLUMN tipe ENUM('apel', 'kegiatan', 'kegiatan_langsung') DEFAULT 'kegiatan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE kegiatan MODIFY COLUMN tipe ENUM('apel', 'kegiatan') DEFAULT 'kegiatan'");
    }
};
