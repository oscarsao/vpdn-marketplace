<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'business_id',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo('App\Models\Business');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

}
