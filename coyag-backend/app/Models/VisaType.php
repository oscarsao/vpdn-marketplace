<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'flag_active'
    ];

    public function AddedServices(): HasMany
    {
        return $this->hasMany('App\Models\AddedService');
    }

    /* Relación muchos a muchos visa_document_types */
    public function visaDocumentType(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\VisaDocumentType', 'visa_document_type_visa_type')
            ->withPivot('visa_document_type_id')
            ->withTimestamps();
    }

    /* Relación muchos a muchos visa_step_types */
    public function visaStepType(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\VisaStepType', 'visa_step_type_visa_type')
            ->withPivot('visa_step_type_id', 'number_client', 'number_advisor')
            ->withTimestamps();
    }
}
