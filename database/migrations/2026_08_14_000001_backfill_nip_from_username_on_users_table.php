<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Backfill kolom users.nip yang kosong dari users.username (atau pegawai.nip).
     * Login memakai Auth::attempt(['nip' => ...]), jadi users.nip harus terisi.
     */
    public function up(): void
    {
        // Pass 1: isi nip dari username (username = NIP, unik).
        $users = DB::table('users')
            ->where(function ($query) {
                $query->whereNull('nip')->orWhere('nip', '');
            })
            ->whereNotNull('username')
            ->where('username', '!=', '')
            ->get(['id', 'username']);

        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update(['nip' => $user->username]);
        }

        // Pass 2: isi sisa nip kosong dari pegawai.nip.
        $remaining = DB::table('users')
            ->join('pegawai', 'users.pegawai_id', '=', 'pegawai.id')
            ->where(function ($query) {
                $query->whereNull('users.nip')->orWhere('users.nip', '');
            })
            ->whereNotNull('pegawai.nip')
            ->where('pegawai.nip', '!=', '')
            ->select('users.id', 'pegawai.nip')
            ->get();

        foreach ($remaining as $row) {
            DB::table('users')->where('id', $row->id)->update(['nip' => $row->nip]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill tidak bisa dibalik dengan aman.
    }
};
