<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provinces_list',
        'min_investment',
        'max_investment',
        'sectors_list',
        'business_types_list',
        'business_list'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_list'     =>  'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

}
