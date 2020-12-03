<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class PaymentMethod extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
    protected $table = 'paymentmethods';
    // Relationships
public function orders(){
    return $this->belongsToMany(Order::class);
}
public function funds(){
    return $this->belongsToMany(Fund::class);
}
}
