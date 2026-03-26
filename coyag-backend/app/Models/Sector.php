<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /* Relación muchos a muchos businesses */
    public function business(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Business', 'business_sector')
            ->withPivot('business_id')
            ->withTimestamps();
    }
}
