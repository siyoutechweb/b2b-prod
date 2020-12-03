<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statut extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function orders() {
        return $this->hasMany(Order::class);
    }

}
