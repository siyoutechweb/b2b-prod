<?php namespace App\Models;
use App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

  public function paymentmethods(){
      return $this->BelongsTo(PaymentMethod::class, 'payment_method_id');
  }

  public function order(){
    return $this->BelongsTo(order::class, 'order_id');
}
public function shop_owner(){
    return $this->BelongsTo(user::class, 'shop_owner_id');
}

public function supplier(){
    return $this->BelongsTo(user::class, 'supplier_id');
}

}
