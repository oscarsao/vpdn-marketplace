<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailManagement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'employee_id',
        'type',
        'is_send',
        'sending_date',
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

}
