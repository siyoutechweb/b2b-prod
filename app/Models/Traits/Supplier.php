<?php

namespace App\Models\Traits;

use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBase;
use App\Models\Slide;
use App\Models\Supplier_Salesmanager_ShopOwner;

trait Supplier
{
    public function suppliers()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'salesmanager_id', 'supplier_id')->withPivot(['shop_owner_id', 'commission_amount'])->withTimestamps();
    }

    public function shop_owners()
    {
        return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner','supplier_id', 'shop_owner_id')->withPivot(['shop_owner_id', 'commission_amount'])->withTimestamps();
    }

    // public function getShopsThroughSalesManager()
    // {
    //     return $this->hasManyThrough(Supplier_Salesmanager_ShopOwner::class, Supplier_Salesmanager_ShopOwner::class, 'supplier_id', 'id', 'id', 'salesmanager_id');
    // }

    public function getShopsThroughOrder()
    {
        return $this->hasManyThrough(User::class, Order::class, 'supplier_id', 'id', 'id', 'shop_owner_id');
    }

    public function getSupplierCategoryThroughProduct() {
        return $this->hasManyThrough(Category::class, Product::class, 'supplier_id', 'id', 'id', 'category_id');
    }

    public function getCategoryThroughProduct() {
        return $this->hasManyThrough(Category::class, ProductBase::class, 'supplier_id', 'id', 'id', 'category_id');
    }

    public function slides()
    {
        return $this->hasMany(Slide::class,'supplier_id');
    }

    public function product_list()
    {
        return $this->hasMany(ProductBase::class,'supplier_id');
    }
}
