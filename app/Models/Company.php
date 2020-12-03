<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }
}
