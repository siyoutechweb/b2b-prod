<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {

    protected $fillable = [];

    protected $table = 'inventories';

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(SupplierWarehouse::class, 'warehouse_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'inventory_product','inventory_id','product_id')
               ->withPivot(['arrived_quantity','total_quantity','product_status','verified_quantity','unverified_quantity'])->withTimestamps();
    }

    public function wholesalers()
    {
        return $this->belongsTo(Wholesaler::class, 'wholesaler_id');
    }   
    public function verifier_status()
    {
        return $this->belongsTo(Status::class, 'verifier_status');
    }

    public function operator_status()
    {
        return $this->belongsTo(Status::class, 'operator_status');
    }
}
