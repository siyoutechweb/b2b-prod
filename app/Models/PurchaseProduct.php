<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use App\Models\PurchaseOrder;

class PurchaseProduct extends Model {

    protected $table ='purchase_products';
    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    // public function PurchaseOrder()
    // {
    //     return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    // }
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public static function insertProducts ($data, $preOrder, $shop_owner_id){


        foreach ($data as  $value) 
        {
            $arr[] = ['product_name' => $value->product_name, 
                    'product_barcode' => $value->product_barcode,
                    'product_description' => $value->product_description,
                    'product_image' => $value->product_image,
                    'cost_price' => (float)$value->cost_price,
                    'tax_rate' => $value->tax_rate,
                    'product_weight' => $value->product_weight,
                    'product_size' => $value->product_size,
                    'product_color' => $value->product_color,
                    'product_quantity' => $value->product_quantity,
                    'supplier_id' => $value->supplier_id,
                    'shop_owner_id' =>$shop_owner_id,
                    'category_id' => $value->category_id,
                    'purchase_order_id' => $preOrder,
                    ];
        }
       if (!empty($arr)) { DB::table('purchase_products')->insert($arr);}
    }

}
