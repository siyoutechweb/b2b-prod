<?php namespace App\Models\Traits;
use App\Models\User;
trait ShopOwner
{
    public function shopOwners()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'salesmanager_id', 'shop_owner_id')->withPivot(['supplier_id', 'commission_amount'])->withTimestamps();
    }

    public function supplier()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'shop_owner_id', 'supplier_id')->withPivot(['shop_owner_id', 'commission_amount'])->withTimestamps();
    }
}