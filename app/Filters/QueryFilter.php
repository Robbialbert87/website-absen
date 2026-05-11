<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        // Apply sorting
        if ($this->request->has('sort_by')) {
            $this->sort();
        }

        foreach ($this->filters() as $name => $value) {
            if (!method_exists($this, $name)) {
                continue;
            }

            if (strlen($value)) {
                $this->$name($value);
            } else {
                // If the parameter is present but empty, still call it but without value
                // Sometimes useful for flags
                // $this->$name();
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }

    protected function sort()
    {
        $sortBy = $this->request->get('sort_by');
        $sortDir = $this->request->get('sort_dir', 'asc');
        
        // Ensure sort dir is safe
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'asc';

        if ($this->isSortable($sortBy)) {
            $this->builder->orderBy($sortBy, $sortDir);
        }
    }

    /**
     * Define which columns can be sorted.
     */
    protected function isSortable($column)
    {
        return true; // Override in subclass for security if needed
    }
}
