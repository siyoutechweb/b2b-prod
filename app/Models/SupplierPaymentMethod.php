<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPaymentMethod extends Model {
    protected $table = 'supplier_payment_methods';
    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    // public function paymentmethods(){
    //     return $this->BelongsTo(PaymentMethod::class, 'payment_method_id');
    // }
    // public function supplier() {
    //     return $this->belongsTo(Supplier::class, 'supplier_id');
    // }

}