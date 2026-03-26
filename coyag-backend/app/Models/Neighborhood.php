<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Neighborhood extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id',
        'name',
        'rent_per_capita',
        'demographic_data'
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo('App\Models\District');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany('App\Models\Business');
    }

}
