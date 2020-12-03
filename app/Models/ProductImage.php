<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model {

    protected $table ='product_images';

    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function Item() {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

}
