<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sync-pegawai-user')]
#[Description('Command description')]
class SyncPegawaiUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pegawais = \App\Models\Pegawai::all();
        $count = 0;
        foreach ($pegawais as $pegawai) {
            $user = \App\Models\User::firstOrCreate(
                ['pegawai_id' => $pegawai->id],
                [
                    'name' => $pegawai->nama,
                    'username' => $pegawai->nip,
                    'password' => \Illuminate\Support\Facades\Hash::make($pegawai->nip),
                    'ruangan_id' => $pegawai->ruangan_id,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->assignRole('user');
                $count++;
            } else {
                if (empty($user->username)) {
                    $user->update(['username' => $pegawai->nip]);
                    $count++;
                }
            }
        }
        $this->info("Successfully synced {$count} pegawai to users.");
    }
}
