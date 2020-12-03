<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships defining
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function products() {
        return $this->belongsToMany(Product::class, 'product_catalog', 'catalog_id', 'product_id');
    }

}
