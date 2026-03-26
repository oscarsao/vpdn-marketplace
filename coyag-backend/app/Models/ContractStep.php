<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStep extends Model
{
    use HasFactory;

    public function contract() {
        return $this->belongsTo(Contract::class);
    }
    public function files () {
        return $this->hasMany(File::class);
    }
}
