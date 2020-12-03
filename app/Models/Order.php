<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class Order extends Model
{

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    // public function products() {
    //     return $this->belongsToMany(Product::class, 'product_order', 'order_id', 'product_id');
    // }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_order', 'order_id', 'product_id')->withPivot(['quantity'])->withTimestamps();
    }
    public function paymentmethods(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function productItem()
    {
        return $this->belongsToMany(ProductItem::class, 'item_order','order_id', 'item_id')->withPivot(['quantity'])->withTimestamps();
    }
 

    public function shopOwner() {
        return $this->belongsTo(User::class, 'shop_owner_id');
    }
    
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function commissions()
    {
        // return $this->hasOne(Commission::class);
        return $this->belongsTo(Commission::class, 'commission');
    }

    public function statut()
    {
        return $this->belongsTo(Statut::class, 'statut_id');
    }

    //functions
    public static function addOrderToS2C($order, $supplier)
    {
        $shop_email = user::where('id',$order->shop_owner_id)->value('email');
        $shop_owner_id = DB::connection('S2C')->table('users')->select('*')->where('email',$shop_email)->value('id');

        if (!empty($shop_owner_id))
        { 
            $s2c_supplier = DB::connection('S2C')->table('suppliers')->where('email',$supplier->email)->value('id');
            if (empty($s2c_supplier)) { self::createS2CSupplier($supplier); } // search if supplier exist in s2c if not add row to s2c.suppliers table

            $items = $order->productItem()->with(['CriteriaBase','images','product'=>function ($q)
            {$q->with('brand:id,brand_name','category:id,category_name','supplier:id,first_name,last_name')
                ->get();}])->get();
            $updateQuantity = self::updateItemQuantity($items);   
            $preOrder= self::newPreorder($order,$s2c_supplier, $shop_owner_id); // add preOrder to s2c system
            $preProduct = self::purchaseProduct($items, $preOrder, $s2c_supplier, $shop_owner_id); // add product related to preOrder
         
        }
        else 
        {
            $items = $order->productItem;
            $updateQuantity = self::updateItemQuantity($items);
        }
        
    }

    private static function createS2CSupplier($supplier)
    {
         $s2c_supplier = DB::connection('S2C')->table('suppliers')->insertGetId(
        ["first_name" => $supplier->first_name,
        "last_name" => $supplier->last_name,
        "email" => $supplier->email,
        "img_url" => $supplier->img_url,
        "img_name" => $supplier->img_name,
        "latitude" => $supplier->latitude,
        "longitude" => $supplier->latitude,]);
        return $s2c_supplier;
    }

    private static function newPreorder($order,$s2c_supplier, $shop_owner_id)
    {
        $preOrder = DB::connection('S2C')->table('purchase_orders')->insertGetId([
            "order_ref" => $order->order_ref,
            "order_date" => $order->order_date,
            "order_price" => $order->order_price,
            "supplier_id" => $s2c_supplier,
            "shop_owner_id" => $shop_owner_id,
            "shipping_date" => $order->shipping_date,
            "shipping_type" => $order->shipping_type,
            "shipping_price" => $order->shipping_price,
            "shipping_adresse" => $order->shipping_adresse,
            "shipping_country" => $order->shipping_country,
            "statut_id" => 1,
            "created_at" => Carbon::now()
        ]);
        return $preOrder ; 
    }

    private static function purchaseProduct($items, $preOrder, $s2c_supplier, $shop_owner_id)
    {
        foreach ($items as $item) {
            $product = DB::connection('S2C')->table('purchase_products')->insert([
                "product_name" => $item->product->product_name,
                "product_barcode" => $item->item_barcode,
                "product_description" => self::productDescription($item->criteriaBase),
                "product_image" => $item->product->product_image_url,
                "cost_price" => $item->item_offline_price,
                "tax_rate" => $item->product->taxe_rate,
                "product_weight" => null,
                "product_size" => null,
                "product_color" => null,
                "purchase_order_id" =>$preOrder,
                "product_quantity" => $item->pivot->quantity,
                "category_id" => $item->product->category_id,
                "shop_owner_id" => $shop_owner_id,
                "supplier_id" => $s2c_supplier,
                "created_at" => Carbon::now()
            ]); 
        } 
    }

    private static function productDescription($criterias)
    {
        $description = "";
        foreach ($criterias as $key => $criteria) {
            $description .= $criteria->name.": ".$criteria->pivot->criteria_value.' ; ';
        }
        return $description;
    }

    private static function updateItemQuantity($items)
    {
        foreach ($items as $item) 
        {
            $item->item_quantity -= $item->pivot->quantity;
            $item->save();
        }
    
    }

}
