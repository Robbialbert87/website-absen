<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Shift extends Model
{
    use Filterable;
    protected $table = 'shifts';
    protected $fillable = [
        'kode_shift', 'nama_shift', 'kategori_jadwal', 'jam_masuk', 'jam_pulang', 'warna', 'keterangan',
        'is_senin', 'is_selasa', 'is_rabu', 'is_kamis', 'is_jumat', 'is_sabtu', 'is_minggu'
    ];

    public function isCrossDay()
    {
        return $this->jam_pulang < $this->jam_masuk;
    }
}
