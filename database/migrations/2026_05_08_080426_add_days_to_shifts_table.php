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
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('is_senin')->default(false)->after('keterangan');
            $table->boolean('is_selasa')->default(false)->after('is_senin');
            $table->boolean('is_rabu')->default(false)->after('is_selasa');
            $table->boolean('is_kamis')->default(false)->after('is_rabu');
            $table->boolean('is_jumat')->default(false)->after('is_kamis');
            $table->boolean('is_sabtu')->default(false)->after('is_jumat');
            $table->boolean('is_minggu')->default(false)->after('is_sabtu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['is_senin', 'is_selasa', 'is_rabu', 'is_kamis', 'is_jumat', 'is_sabtu', 'is_minggu']);
        });
    }
};
