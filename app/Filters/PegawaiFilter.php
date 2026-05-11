<?php

namespace App\Filters;

class PegawaiFilter extends QueryFilter
{
    public function search($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('nama', 'like', "%{$value}%")
                  ->orWhere('nip', 'like', "%{$value}%")
                  ->orWhere('jabatan', 'like', "%{$value}%");
        });
    }

    public function ruangan_id($value)
    {
        return $this->builder->where('ruangan_id', $value);
    }

    public function kategori_kerja($value)
    {
        return $this->builder->where('kategori_kerja', $value);
    }

    public function status_aktif($value)
    {
        return $this->builder->where('status_aktif', $value);
    }

    protected function isSortable($column)
    {
        $sortableColumns = ['nip', 'nama', 'kategori_kerja', 'ruangan_id', 'jabatan', 'status_aktif'];
        return in_array($column, $sortableColumns);
    }
}
