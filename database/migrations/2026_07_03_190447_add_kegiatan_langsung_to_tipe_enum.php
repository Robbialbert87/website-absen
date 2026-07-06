<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE kegiatan MODIFY COLUMN tipe ENUM('apel', 'kegiatan', 'kegiatan_langsung') DEFAULT 'kegiatan'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE kegiatan MODIFY COLUMN tipe ENUM('apel', 'kegiatan') DEFAULT 'kegiatan'");
        }
    }
};
