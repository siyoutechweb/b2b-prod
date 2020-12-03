<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Wholesaler extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function whole_saler()
    {
        return $this->belongsToMany(User::class,'supplier_wholeSaler','supplier_id','wholesaler_id')->withTimestamps();
    }

}
