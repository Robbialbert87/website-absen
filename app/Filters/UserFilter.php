<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    public function search($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%");
        });
    }

    public function role($value)
    {
        return $this->builder->whereHas('roles', function ($query) use ($value) {
            $query->where('name', $value);
        });
    }

    public function ruangan_id($value)
    {
        return $this->builder->whereHas('pegawai', function ($query) use ($value) {
            $query->where('ruangan_id', $value);
        });
    }

    protected function isSortable($column)
    {
        $sortableColumns = ['name', 'created_at'];
        return in_array($column, $sortableColumns);
    }
}
