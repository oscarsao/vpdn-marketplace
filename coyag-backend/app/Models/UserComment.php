<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment'
    ];

    public function user_comment_type(): BelongsTo
    {
        return $this->belongsTo('App\Models\UserCommentType');
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
