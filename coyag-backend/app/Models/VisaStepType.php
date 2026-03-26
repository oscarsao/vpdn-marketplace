<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VisaStepType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_description',
        'advisor_description'
    ];


    /* Relación muchos a muchos visa_types */
    public function visaType(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\VisaType', 'visa_step_type_visa_type')
            ->withPivot('visa_type_id', 'number_client', 'number_advisor')
            ->withTimestamps();
    }
}
