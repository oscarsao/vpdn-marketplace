<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddedService extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'service_id',
        'flag_active_plan',
        'plan_deactivated_at',
        'flag_payment_completed',
        'file_id', 'arrival_date',
        'visa_type_id',
        'monday_form',
        'monday_iframe',
        'monday_gantt_vp'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo('App\Models\Service');
    }

    public function financialAnalyses(): HasMany
    {
        return $this->hasMany('App\Models\FinancialAnalysis');
    }

    /* Relación muchos a muchos con pagos */

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Payment','added_service_payment')
            ->withTimestamps();
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo('App\Models\File');
    }

    public function visaType(): BelongsTo
    {
        return $this->belongsTo('App\Models\VisaType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visaRequirements(): HasMany
    {
        return $this->hasMany('App\Models\VisaRequirement');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visaSteps(): HasMany
    {
        return $this->hasMany('App\Models\VisaStep');
    }
}
