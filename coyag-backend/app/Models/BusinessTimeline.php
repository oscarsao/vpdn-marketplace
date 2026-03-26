<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTimeline extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'employee_id',
        'module',
        'type_crud',
        'business_multimedia',
        'message'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo('App\Models\Business');
    }
}
