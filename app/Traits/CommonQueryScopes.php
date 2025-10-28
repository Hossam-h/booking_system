<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function scopeFilterByDate(Builder $query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeSearchByTitle(Builder $query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }
}