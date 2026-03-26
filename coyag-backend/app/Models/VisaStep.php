<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaStep extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'added_service_id',
        'name',
        'client_description',
        'advisor_description',
        'status',
        'number_client',
        'number_advisor',
        'date_completed'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function addedService(): BelongsTo
    {
        return $this->belongsTo('App\Models\AddedService');
    }

}
