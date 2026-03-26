<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'employee_id',
        'file_id',
        'bank',
        'no_transaction',
        'amount',
        'observation',
        'currency'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo('App\Models\File');
    }

    public function financial_analyses(): HasMany
    {
        return $this->hasMany('App\Models\FinancialAnalysis');
    }

    /* Relación muchos a muchos con AddedService */

    public function addedServices(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\AddedService','added_service_payment')
            ->withTimestamps();
    }

}
