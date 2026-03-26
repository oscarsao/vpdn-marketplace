<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientTimeline extends Model
{
    use HasFactory, Softdeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'employee_id',
        'module',
        'module_eng',
        'type_crud',
        'type_crud_eng',
        'message',
        'properties'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\Models\Client');
    }
}
