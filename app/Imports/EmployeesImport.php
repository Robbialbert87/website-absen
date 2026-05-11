<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Get or Create Ruangan
        $ruangan = Ruangan::firstOrCreate(
            ['kode_ruangan' => strtoupper($row['ruangan'])],
            ['nama_ruangan' => $row['ruangan'], 'keterangan' => 'Auto created from import']
        );

        // 2. Upsert Pegawai by NIP
        return Pegawai::updateOrCreate(
            ['nip' => $row['nip']],
            [
                'nama' => $row['nama'],
                'ruangan_id' => $ruangan->id,
                'jabatan' => $row['jabatan'],
                'kategori_kerja' => $row['kategori_kerja'] ?? 'non_shift',
                'status_aktif' => (bool)$row['status_aktif'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nip' => 'required',
            'nama' => 'required',
            'ruangan' => 'required',
            'jabatan' => 'required',
            'kategori_kerja' => 'nullable|in:non_shift,shift',
            'status_aktif' => 'required',
        ];
    }
}
