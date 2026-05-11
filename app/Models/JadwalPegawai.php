<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'ruangan_id',
        'shift_id',
        'tanggal_masuk',
        'jam_masuk',
        'tanggal_pulang',
        'jam_pulang',
        'kode_shift',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
