<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMultimedia extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'type',
        'link_video',
        'original_name',
        'extension',
        'size',
        'mime_type',
        'path',
        'large_image_path',
        'medium_image_path',
        'small_image_path',
        'type_client'
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo('App\Models\Business');
    }

}
