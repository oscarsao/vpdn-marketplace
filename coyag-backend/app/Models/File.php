<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'employee_id',
        'original_name',
        'name',
        'extension',
        'size',
        'mime_type',
        'path',
        'full_path'
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne('App\Models\Payment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function addedService(): HasOne
    {
        return $this->hasOne('App\Models\AddedService');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function visaRequirement(): BelongsTo
    {
        return $this->belongsTo(VisaRequirement::class, 'id_visa_requirement', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dossier(): HasOne
    {
        return $this->hasOne('App\Models\Dossier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function busiest(): HasOne
    {
        return $this->hasOne('App\Models\Busiest');
    }

    public function contractStep(): BelongsTo {
        return $this->belongsTo(ContractStep::class);
    }

}
