<?php

namespace App\Filters;

class ShiftFilter extends QueryFilter
{
    public function search($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('kode_shift', 'like', "%{$value}%")
                  ->orWhere('nama_shift', 'like', "%{$value}%");
        });
    }

    public function kategori_jadwal($value)
    {
        return $this->builder->where('kategori_jadwal', $value);
    }

    public function jam_kerja($value) // Example: "08:00-16:00"
    {
        if (strpos($value, '-') !== false) {
            [$start, $end] = explode('-', $value);
            return $this->builder->where('jam_masuk', '>=', trim($start))
                                 ->where('jam_pulang', '<=', trim($end));
        }
        return $this->builder;
    }

    protected function isSortable($column)
    {
        $sortableColumns = ['kode_shift', 'nama_shift', 'kategori_jadwal', 'jam_masuk', 'jam_pulang', 'created_at'];
        return in_array($column, $sortableColumns);
    }
}
