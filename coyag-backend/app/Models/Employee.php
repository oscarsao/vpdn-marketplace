<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'second_name',
        'surname',
        'second_surname',
        'identification_card',
        'civil_status',
        'birthdate',
        'admission_date',
        'address',
        'personal_email',
        'corporate_email',
        'nif',
        'fiscal_address',
        'job_title',
        'salary',
        'headquarter_id',
        'bank_account',
        'mobile_phone',
        'landline',
        'corporate_mobile_phone',
        'corporate_local_phone',
        'emergency_contact',
        'emergency_phone',
        'blood_type',
        'department_id',
        'color',
        'flag_permission',
        'observation_flag_permission'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function headquarter(): BelongsTo
    {
        return $this->belongsTo('App\Models\Headquarter');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany('App\Models\Business');
    }

    public function business_timelines(): HasMany
    {
        return $this->hasMany('App\Models\BusinessTimeline');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany('App\Models\Contact');
    }

    /* Archivos que ha subido */
    public function files(): HasMany
    {
        return $this->hasMany('App\Models\File');
    }

    /* Pagos que ha registrado */
    public function payments(): HasMany
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function clientRequest(): HasMany
    {
        return $this->hasMany('App\Models\ClientRequest');
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

    public function userComments(): HasMany
    {
        return $this->hasMany('App\Models\UserComment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calendars(): HasMany
    {
        return $this->hasMany('App\Models\Calendar');
    }

    public function emailManagements(): HasMany
    {
        return $this->hasMany('App\Models\EmailManagement');
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

    ////////////////////////////////////////////////////////////

    public function contracts() {
        return $this->hasMany(Contract::class, 'gestor_id');
    }
}