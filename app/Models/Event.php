<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonQueryScopes;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    use CommonQueryScopes;
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }



  protected static function booted()
{
    static::created(function ($model) {
        Cache::flush();
    });

    static::updated(function ($model) {
        Cache::flush();
    });

    static::deleted(function ($model) {
        Cache::flush();
    });
}

}
