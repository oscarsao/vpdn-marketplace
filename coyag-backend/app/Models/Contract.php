<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model {
    use HasFactory;

    public function steps() {
        return $this->hasMany(ContractStep::class);
    }
    public function area() {
        return $this->belongsTo(Area::class);
    }
    public function client() {
        return $this->belongsTo(Client::class);
    }
    public function gestor() {
        return $this->belongsTo(Employee::class);
    }
}
