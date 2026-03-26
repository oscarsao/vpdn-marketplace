<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'family_type_id',
        'first_nationality_id',
        'second_nationality_id',
        'names',
        'surnames',
        'email',
        'phone_office',
        'phone_mobile',
        'landline',
        'birthdate',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function familyType(): BelongsTo
    {
        return $this->belongsTo('App\Models\FamilyType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visaRequirements(): HasMany
    {
        return $this->hasMany('App\Models\VisaRequirement');
    }

}
