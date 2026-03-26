<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'municipality_id',
        'business_type_id',
        'employee_id',
        'name',
        'description',
        'address',
        'zip_code',
        'size',
        'lat',
        'lng',
        'age',
        'website',
        'link',
        'private_comment',
        'data_of_interest',
        'relevant_advantages',
        'monthly_billing',
        'contact_name',
        'contact_landline',
        'contact_mobile_phone',
        'contact_email',
        'amount_requested_by_seller',
        'amount_offered_by_us',
        'investment',
        'royalty',
        'rental',
        'contract',
        'minimum_population',
        'canon_of_advertising',
        'canon_of_entrance',
        'flag_exclusive',
        'flag_active',
        'flag_sold',
        'flag_outstanding',
        'times_viewed',
        'rental_contract_years',
        'franchise_contract_years',
        'rental_contract_years_left',
        'franchise_contract_years_left',
        'district_id',
        'id_code',
        'neighborhood_id',
        'return_of_investment',
        'recommendation_finished_at',
        'working_hours',
        'full_time_employees',
        'employees_part_time',
        'managers',
        'gross_revenue',
        'gross_profit',
        'expenses',
        'net',
        'owner_salary',
        'link_map',
        'good_month_revenue',
        'bad_month_revenue',
        'advertiser_owner_type',
        'facade_size',
        'flag_smoke_outlet',
        'flag_terrace',
        'random_string',
        'business_images_string',
        'business_videos_string',
        'collector_id'
    ];

    protected $guarded = [
        'id_business_platform',
        'price_per_sqm',
        'source_platform',
        'source_url',
        'source_timestamp',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo('App\Models\Municipality');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo('App\Models\District');
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo('App\Models\Neighborhood');
    }

    public function business_type(): BelongsTo
    {
        return $this->belongsTo('App\Models\BusinessType');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function business_multimedia(): HasMany
    {
        return $this->hasMany('App\Models\BusinessMultimedia');
    }

    public function business_timelines(): HasMany
    {
        return $this->hasMany('App\Models\BusinessTimeline');
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany('App\Models\Dossier');
    }

    public function busiests(): HasMany
    {
        return $this->hasMany('App\Models\Busiest');
    }

    /* Relación muchos a muchos favorites */

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Client','favorites')
            ->withPivot('client_id')
            ->withTimestamps();
    }


    /* Relación muchos a muchos sectors */

    public function sector(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Sector','business_sector')
            ->withPivot('sector_id')
            ->withTimestamps();
    }

    /* Relación Intermedia de recomendaciones de negocios a clientes */
    public function recommendations(): HasMany
    {
        return $this->hasMany('App\Models\Recommendation');
    }
}
