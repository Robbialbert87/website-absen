<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiKegiatan extends Model
{
    use HasFactory;

    protected $table = 'absensi_kegiatan';
    protected $fillable = [
        'kegiatan_id',
        'pegawai_id',
        'waktu_absen',
        'foto',
        'latitude',
        'longitude',
        'status',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
