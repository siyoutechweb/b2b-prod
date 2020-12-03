<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
    // Relationships

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

}
