<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaRequirement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'visa_document_type_id',
        'added_service_id',
        'client_id',
        'family_id',
        'file_id',
        'expiration_date',
        'status',
        'passport_number',
        'application_date',
        'date_of_issue',
        'observation',
    ];


    public function visaDocumentType (): BelongsTo
    {
        return $this->belongsTo('App\Models\VisaDocumentType');
    }


    public function addedService(): BelongsTo
    {
        return $this->belongsTo('App\Models\AddedService');
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }


    public function family(): BelongsTo
    {
        return $this->belongsTo('App\Models\Family');
    }


    public function files(): hasMany
    {
        return $this->hasMany('App\Models\File', 'id_visa_requirement');
    }

}
