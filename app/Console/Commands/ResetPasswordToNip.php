<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:reset-password-to-nip {nip? : NIP user (kosongkan untuk reset semua)}')]
#[Description('Reset password user ke NIP. Bisa 1 user spesifik atau semua user.')]
class ResetPasswordToNip extends Command
{
    public function handle()
    {
        $nip = $this->argument('nip');

        if ($nip) {
            $user = User::where('nip', $nip)->first();

            if (!$user) {
                $this->error("User dengan NIP '{$nip}' tidak ditemukan.");
                return 1;
            }

            $user->update(['password' => Hash::make($user->nip)]);
            $this->info("Password {$user->name} ({$user->nip}) berhasil direset ke NIP.");
        } else {
            $users = User::whereNotNull('nip')->get();
            $count = 0;

            foreach ($users as $user) {
                $user->update(['password' => Hash::make($user->nip)]);
                $this->line("  Reset password: {$user->name} ({$user->nip})");
                $count++;
            }

            $this->info("{$count} user berhasil direset password-nya ke NIP.");
        }

        return 0;
    }
}
