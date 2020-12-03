<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiyouCommission extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
protected $table='siyoucommission';
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
