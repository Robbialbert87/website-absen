<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove all spaces and non-digit characters from NIP
        // This handles various types of spaces (regular space, non-breaking space, etc.)
        DB::statement("UPDATE pegawai SET nip = REGEXP_REPLACE(nip, '[^0-9]', '')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada yang perlu di-reverse
    }
};
