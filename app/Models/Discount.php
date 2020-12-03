<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function items() {
        return $this->belongsToMany(ProductItem::class,'discount_item','item_id','discount_id')
        ->withPivot(['start_date','finish_date','discount_value'])->withTimestamps();
    }

}
