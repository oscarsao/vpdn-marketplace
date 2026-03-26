<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_nationality_id',
        'second_nationality_id',
        'phone_office',
        'phone_mobile',
        'landline',
        'country_id',
        'birthdate',
        'home_address',
        'office_address',
        'titulation_id',
        'working_years',
        'family_members',
        'converted_to_lite_date',
        'converted_to_premium_date',
        'converted_to_registered_date',
        'is_subscribed_biz_pref',
        'biz_pref_unsubscribe_token',
        'reason_biz_pref_unsubscribe',
        'registration_method'
    ];

    /* Usuario al que pertenece */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo('App\Models\Nationality');
    }

    public function titulation(): BelongsTo
    {
        return $this->belongsTo('App\Models\Titulation');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo('App\Models\Country');
    }

    /* Archivos que le pertenecen */
    public function files(): HasMany
    {
        return $this->hasMany('App\Models\File');
    }

    /* Pagos realizados */
    public function payments(): HasMany
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function clientRequest(): HasMany
    {
        return $this->hasMany('App\Models\ClientRequest');
    }

    /* Relación muchos a muchos favorites */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Business', 'favorites')
            ->withTimestamps();
    }

    public function client_timelines(): HasMany
    {
        return $this->hasMany('App\Models\ClientTimeline');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function video_calls(): HasMany
    {
        return $this->hasMany('App\Models\VideoCall');
    }

    public function AddedServices(): HasMany
    {
        return $this->hasMany('App\Models\AddedService');
    }

    public function userComments(): HasMany
    {
        return $this->hasMany('App\Models\UserComment');
    }

    public function families(): HasMany
    {
        return $this->hasMany('App\Models\Family');
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calendars(): HasMany
    {
        return $this->hasMany('App\Models\Calendar');
    }

    /* Relación Intermedia de recomendaciones de negocios a clientes */
    public function recommendations(): HasMany
    {
        return $this->hasMany('App\Models\Recommendation');
    }

    public function emailManagements(): HasMany
    {
        return $this->hasMany('App\Models\EmailManagement');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function businessClient(): HasOne
    {
        return $this->hasOne('App\Models\BusinessClient');
    }

    /**
     * Get all of the walletTransactions for the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany('App\Models\WalletTransaction');
    }


    ///////////////////////////////////////////////////////

    
    public function contracts() {
        return $this->hasMany(Contract::class);
    }
}
