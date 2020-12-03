<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function catalogs() {
        return $this->belongsToMany(Catalog::class, 'product_catalog', 'product_id', 'catalog_id');
    }
    public function warehouses() {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse', 'product_id', 'warehouse_id');
    }
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function supplier() {
        return $this->belongsTo(User::class, 'supplier_id');
    }
    public function orders() {
        $this->belongsToMany(Product::class, 'product_order', 'product_id', 'order_id')->withTimestamps();;
    }

}
