<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'employee_id',
        'notification_type_id',
        'title',
        'message',
        'flag_read',
        'url'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function notification_type(): BelongsTo
    {
        return $this->belongsTo('App\Models\NotificationType');
    }

}
