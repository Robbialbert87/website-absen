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
        // Compatible with both MySQL and SQLite
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE pegawai SET nip = REGEXP_REPLACE(nip, '[^0-9]', '')");
        } else {
            $pegawais = DB::table('pegawai')->whereNotNull('nip')->get();
            foreach ($pegawais as $p) {
                $clean = preg_replace('/[^0-9]/', '', $p->nip);
                if ($clean !== $p->nip) {
                    DB::table('pegawai')->where('id', $p->id)->update(['nip' => $clean]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada yang perlu di-reverse
    }
};
