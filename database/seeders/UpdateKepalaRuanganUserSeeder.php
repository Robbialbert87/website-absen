<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateKepalaRuanganUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with kepala_ruangan role
        $kepalaRuanganUsers = User::role('kepala_ruangan')->get();

        foreach ($kepalaRuanganUsers as $user) {
            if ($user->pegawai) {
                // Update NIP from pegawai
                $user->nip = $user->pegawai->nip;
                
                // Update password to NIP
                $user->password = Hash::make($user->pegawai->nip);
                
                $user->save();
                
                $this->command->info("Updated user {$user->name} (NIP: {$user->pegawai->nip}) - Password reset to NIP");
            }
        }

        $this->command->info("Selesai! {$kepalaRuanganUsers->count()} kepala ruangan user telah diupdate.");
    }
}

