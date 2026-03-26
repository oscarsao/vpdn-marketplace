<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityByIp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'activity',
        'status',
        'user_id'
    ];

    /**
     * Por ahora no hago la relación muchos a muchos porque solo es una tabla informativa
     */
}
