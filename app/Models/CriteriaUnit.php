<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaUnit extends Model {

    protected $table ='criteria_units';

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function ProductItem() {
        return $this->belongsToMany(ProductItem::class, 'item_criteria', 'product_item_id', 'criteria_id')->withPivot();
    }

    public function CriteriaBase() {
        return $this->belongsTo(CriteriaBase::class,'criteria_base_id');
    }

    // public function CriteriaBase() {
    //     return $this->belongsToMany(CriteriaBase::class,'criteria_unit', 'criteria_id', 'unit_id');
    // }

}
