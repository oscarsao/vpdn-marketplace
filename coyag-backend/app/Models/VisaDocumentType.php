<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaDocumentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visaRequirements(): HasMany
    {
        return $this->hasMany('App\Models\VisaRequirement');
    }

    /* Relación muchos a muchos visa_steps */
    public function visaType(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\VisaType', 'visa_document_type_visa_type')
            ->withPivot('visa_type_id')
            ->withTimestamps();
    }
}
