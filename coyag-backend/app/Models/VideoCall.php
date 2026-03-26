<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'video_call_type_id',
        'client_id',
        'employee_id',
        'list_of_business',
        'status',
        'client_availability',
        'date_and_time',
        'report'
    ];

    public function video_call_type(): BelongsTo
    {
        return $this->belongsTo('App\Models\VideoCallType',);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

}
