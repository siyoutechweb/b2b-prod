<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBase extends Model {

    protected $table ='product_base';

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function supplier() {
        return $this->belongsTo(User::class, 'supplier_id');
    }
    public function orders() {
        return $this->belongsToMany(Product::class, 'product_order', 'product_id', 'order_id')->withTimestamps();;
    }
    
    public function wish_list() {
        return $this->belongsToMany(User::class, 'wish_list', 'product_base_id','user_id')
        ->withTimestamps();
    }

    public function items() {
        return $this->hasMany(ProductItem::class, 'product_base_id');
    }

    public function item() {
        return $this->hasOne(ProductItem::class, 'product_base_id');
    }

    public function Brand() {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function($model)
    //     {
    //         if ($model->forceDeleting) {
    //             $model->roles()->detach();
    //         }
    //     });
    // }

}
