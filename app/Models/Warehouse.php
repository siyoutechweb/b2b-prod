<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
    protected $table='warehouses';

    public function products() {
        return $this->belongsToMany(Product::class, 'product_warehouse', 'warehouse_id', 'product_id')->withPivot(['quantity'],['user_id'])->withTimestamps();
    }
public function user(){
    return $this->belongsTo(User::class, 'user_id');
}
}
