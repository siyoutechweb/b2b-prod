<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    public function companies(){
        return $this->belongsTo(Company::class);
    }
}
