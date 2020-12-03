<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaBase extends Model
{

    protected $table = 'criteria_base';

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function CriteriaUnit()
    {
        return  $this->hasMany(CriteriaUnit::class);
    }
    // public function CriteriaUnit()
    // {
    //     return  $this->belongsToMany(CriteriaUnit::class, 'criteria_unit', 'criteria_id', 'unit_id');
    // }

    // protected $hidden = ['pivot'];
    public function Categories()
    {
        return  $this->belongsToMany(Category::class, 'categories_criteria', 'criteria_id', 'category_id');
    }

    public function productItems()
    {
        return $this->belongsToMany(ProductItem::class, 'items_criteria', 'criteria_id', 'product_item_id')
            ->withPivot(['criteria_value', 'criteria_unit_id'])
            ->withTimestamps();
    }
}
