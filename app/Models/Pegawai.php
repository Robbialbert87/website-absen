<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Pegawai extends Model
{
       use HasFactory, Filterable;
    protected $table = 'pegawai';
    protected $fillable = ['nip', 'nama', 'ruangan_id', 'jabatan', 'kategori_kerja', 'shift_id', 'status_aktif'];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
