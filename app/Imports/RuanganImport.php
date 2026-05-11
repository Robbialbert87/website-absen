<?php

namespace App\Imports;

use App\Models\Ruangan;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class RuanganImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    public function onFailure(Failure ...$failures)
    {
        // Failures are handled by the controller
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Log keys once to check if they match our expectations
        static $logged = false;
        if (!$logged) {
            \Log::info('Import Row Keys: ' . implode(', ', array_keys($row)));
            $logged = true;
        }

        if (empty($row['kode_ruangan'])) {
            return null;
        }

        $kepala_pegawai_id = null;
        if (!empty($row['kepala_nip'])) {
            $pegawai = \App\Models\Pegawai::where('nip', $row['kepala_nip'])->first();
            if ($pegawai) {
                $kepala_pegawai_id = $pegawai->id;
            }
        }

        return Ruangan::updateOrCreate(
            ['kode_ruangan' => strtoupper($row['kode_ruangan'])],
            [
                'nama_ruangan' => $row['nama_ruangan'],
                'keterangan' => $row['keterangan'] ?? null,
                'kepala_pegawai_id' => $kepala_pegawai_id,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode_ruangan' => 'required',
            'nama_ruangan' => 'required',
            'keterangan' => 'nullable',
            'kepala_nip' => 'nullable',
        ];
    }
}
