<?php

namespace App\Filters;

class RuanganFilter extends QueryFilter
{
    public function search($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('nama_ruangan', 'like', "%{$value}%")
                  ->orWhere('kode_ruangan', 'like', "%{$value}%");
        });
    }

    public function kepala_pegawai_id($value)
    {
        return $this->builder->where('kepala_pegawai_id', $value);
    }

    protected function isSortable($column)
    {
        $sortableColumns = ['kode_ruangan', 'nama_ruangan', 'kepala_pegawai_id', 'created_at'];
        return in_array($column, $sortableColumns);
    }
}
