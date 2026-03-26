<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accomplished',
        'flag_active',
        'added_service_id'
    ];

    public function addedService(): BelongsTo
    {
        return $this->belongsTo('App\Models\AddedService');
    }
}
