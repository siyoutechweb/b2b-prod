<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierFund extends Model {
    protected $table = 'supplier_funds';
    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
   public function paymentmethods(){
        return $this->BelongsTo(SupplierPaymentMethod::class, 'payment_method_id');
    }
       public function wholesaler() {
        return $this->belongsTo(Wholesaler::class, 'wholesaler_id');
    }
}