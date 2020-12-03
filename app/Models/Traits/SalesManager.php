<?php namespace App\Models\Traits;
use App\Models\User;
use App\Models\position_tracking;
trait SalesManager
{
    
    public function salesmanagerToSupplier()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'supplier_id', 'salesmanager_id')->withPivot(['shop_owner_id', 'commission_amount'])->withTimestamps();
    }

    public function salesmanagerToShop()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'shop_owner_id', 'salesmanager_id')->withPivot(['supplier_id', 'commission_amount'])->withTimestamps();
    }

    public function position() {
        return $this->hasMany(position_tracking::class);
    }

}