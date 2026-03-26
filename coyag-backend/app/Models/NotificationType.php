<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'user_type',
        'title',
        'message',
        'active',
        'roles_id',
        'replicate_notification'
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany('App\Models\Notification');
    }
}
