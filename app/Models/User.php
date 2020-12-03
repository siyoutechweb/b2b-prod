<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Models\Order;
use App\Models\ProductBase;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    use Traits\SalesManager;
    use Traits\Supplier;
    use Traits\ShopOwner;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'first_name', 'last_name'
    ];
    

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'pivot'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
          
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'email'           => $this->email,
            'contact'         =>$this->contact,
            'confirmed'       => $this->confirmed,
                  ];
    }

    // Relationships defining
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // *******OLD RELATIONSHIPS*******
    // public function salesmanagerToSupplier()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager', 'supplier_id', 'salesmanager_id');
    // }

    // public function suppliers()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager', 'salesmanager_id', 'supplier_id');
    // }

    // public function shopOwners() {
    //     return $this->belongsToMany(User::class, 'salesmanager_shopowner', 'salesmanager_id', 'shop_owner_id');
    // }

    // public function salesmanagerToShop() {
    //     return $this->belongsToMany(User::class, 'salesmanager_shopowner', 'shop_owner_id', 'salesmanager_id');
    // }

    // *******OLD RELATIONSHIPS*******

    // public function salesmanagerToSupplier()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'supplier_id', 'salesmanager_id')->withTimestamps()->withPivot('shop_owner_id');;
    // }

    // public function suppliers()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'salesmanager_id', 'supplier_id')->withTimestamps()->withPivot('shop_owner_id');
    // }

    // public function shopOwners()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'salesmanager_id', 'shop_owner_id')->withTimestamps()->withPivot('supplier_id');
    // }

    // public function salesmanagerToShop()
    // {
    //     return $this->belongsToMany(User::class, 'supplier_salesmanager_shop_owner', 'shop_owner_id', 'salesmanager_id')->withTimestamps()->withPivot('supplier_id');;
    // }


    // public function salesmanager() {
    //     return $this->belongsToMany()
    // }
    public function catalogs()
    {
        return $this->hasMany(Catalog::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
    public function orders()
    {
        return $this->hasMany(order::class, 'supplier_id');
    }
    public function shopOrders()
    {
        return $this->hasMany(order::class, 'shop_owner_id');
    }
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'sales_manager_id');
    }
    public function siyoucommissions()
    {
        return $this->hasOne(SiyouCommission::class, 'supplier_id');
    }

    // public function getShopsThroughOrder()
    // {
    //     return $this->hasManyThrough(User::class, Order::class, 'supplier_id', 'id', 'id', 'shop_owner_id');
    // }

    // public function getShopsFromSalesmanager() {
    //     return $this->hasManyThrough(SalesmanagerShopOwner::class, SupplierSalesmanager::class);
    // }

    // Middleware functions to identify role
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role)
    {
        // var_dump($this->role()->where('name', $role)->first());
        if ($this->role()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }
    public function Categories()
    {
        return $this->hasManyThrough(Category::class, ProductBase::class,'supplier_id','id','id','category_id')->distinct(); 
        // $sql= hasManyThrough(Category::class, ProductBase::class,'supplier_id','id','id','category_id')->toSQL(); 
        // dd($sql);
    }

    public function productBase()
    {
        return $this->hasMany(ProductBase::class, 'supplier_id');
    }

    public function favorit_product() {
        return $this->belongsToMany(ProductBase::class, 'wish_list','user_id', 'product_base_id')
        ->withTimestamps();
    }

    public static $createRules = [
        'email' => 'required|unique:users|max:255',
        'password' => 'required|min:8',
    ];
}
