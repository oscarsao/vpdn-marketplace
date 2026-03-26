<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'flag_active',
        'type',
        'roles_slug',
        'name_payment',
        'slug_payment',
        'recommended_price',
        'flag_recurring_payment',
        'flag_payment_in_installments',
        'financial_analysis_available',
        'flag_mandatory_payment'
    ];

    public function AddedServices(): HasMany
    {
        return $this->hasMany('App\Models\AddedService');
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany('App\Models\ClientRequest');
    }

}
