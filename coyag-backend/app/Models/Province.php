<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'autonomous_community_id'
    ];

    public function municipalities(): HasMany
    {
        return $this->hasMany('App\Models\Municipality');
    }

    public function autonomousCommunity(): BelongsTo
    {
        return $this->belongsTo('App\Models\AutonomousCommunity');
    }

    public function businesses(): HasManyThrough
    {
        return $this->hasManyThrough('App\Models\Business', 'App\Models\Municipality');
    }

}
