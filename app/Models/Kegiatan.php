<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';
    protected $fillable = [
        'nama_kegiatan',
        'tanggal_kegiatan',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'latitude',
        'longitude',
        'radius_meter',
        'status',
        'tipe',
        'created_by',
    ];

    public function absensiKegiatan()
    {
        return $this->hasMany(AbsensiKegiatan::class);
    }

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'kegiatan_pegawai');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
