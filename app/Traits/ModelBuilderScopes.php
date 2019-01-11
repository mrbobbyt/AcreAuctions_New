<?php
declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ModelBuilderScopes
{
    /**
     * Scope a query to only include requested fields
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function scopeWhereFields($query, array $fields)
    {
        $params = collect($fields)->filter(function ($value, $key) {
            return in_array($key, $this->fillable);
        })->all();

        return $query->where($params);
    }

}
