<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierWarehouse extends Model {
    protected $table = 'supplier_warehouses';

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function inventories()
    {
        return $this->belongsTo(Inventory::class, 'warehouse_id');
    }
    
    

}
