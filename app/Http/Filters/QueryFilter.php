<?php
declare(strict_types=1);


namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;
    protected array $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function filter(array $arr): Builder
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    protected function sort(string $value): void
    {
        $sortAttributes = explode(',', $value);

        foreach ($sortAttributes as $attribute) {
            $direction = 'asc';

            if (str_starts_with($attribute, '-')) {
                $direction = 'desc';

                $attribute = substr($attribute, 1);
            }

            //check if the field is sortable
            if (!in_array($attribute, $this->sortable) && !array_key_exists($attribute, $this->sortable)) {
                continue;
            }
            
            $columnName = $this->sortable[$attribute] ?? $attribute;

            $this->builder->orderBy($columnName, $direction);
        }
    }
}
