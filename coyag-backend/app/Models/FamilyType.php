<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class FamilyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function families(): HasMany
    {
        return $this->hasMany('App\Models\Family');
    }
}
