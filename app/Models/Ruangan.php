<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Ruangan extends Model
{
    use Filterable;
    protected $table = 'ruangan';
    protected $fillable = ['kode_ruangan', 'nama_ruangan', 'keterangan', 'kepala_pegawai_id'];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function kepalaPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kepala_pegawai_id');
    }
}
