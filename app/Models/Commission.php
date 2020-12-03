<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $hidden = ['pivot'];

    // Relationships
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasOne(Order::class, 'commission');
    }

    public function items()
    {
        return $this->belongsToMany(ProductItem::class, 'commission_item','commission_id','item_id')->withTimestamps();
    }

    public function shop_owner()
    {
        return $this->belongsTo(User::class,'shop_owner_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class,'supplier_id');
    }

    public function sales_manager()
    {
        return $this->belongsTo(User::class,'salesmanager_id');
    }

}
