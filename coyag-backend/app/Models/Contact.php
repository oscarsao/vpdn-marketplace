<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'message',
        'request_ip',
        'flag_read',
        'flag_answered',
        'employee_id'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }


}
