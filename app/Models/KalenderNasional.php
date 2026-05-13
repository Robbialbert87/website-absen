<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderNasional extends Model
{
    protected $table    = 'kalender_nasional';
    protected $fillable = ['tanggal', 'nama_hari_libur', 'jenis', 'warna', 'status_aktif'];

    protected $casts = [
        'tanggal'      => 'date',
        'status_aktif' => 'boolean',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeNasional($query)
    {
        return $query->where('jenis', 'nasional');
    }

    public function scopeCutiBersama($query)
    {
        return $query->where('jenis', 'cuti_bersama');
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
    }

    // Accessors
    public function getWarnaDisplayAttribute(): string
    {
        return match($this->jenis) {
            'cuti_bersama' => '#ffc107',
            default        => '#e74c3c',
        };
    }
}
