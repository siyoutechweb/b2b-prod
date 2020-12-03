<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Discount;

class ProductItem extends Model {

    protected $table ='product_items';


    

    // Relationships
    public function Product() {
        return $this->belongsTo(ProductBase::class, 'product_base_id');
    }

    public function Images() {
        return $this->hasMany(ProductImage::class, 'product_item_id');
    }

    public function CriteriaBase() {
        return $this->belongsToMany(CriteriaBase::class, 'items_criteria', 'product_item_id', 'criteria_id')
        ->withPivot(['criteria_value', 'criteria_unit_id'])
        ->withTimestamps()->orderby('criteria_id','asc');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'item_order', 'item_id','order_id')->withPivot(['quantity'])->withTimestamps();
    }

    public function Discount() {
        return $this->belongsToMany(Discount::class, 'discount_item','item_id','discount_id')
        ->withPivot(['discount_value','start_date','finish_date']);
    }

    public static function updateDiscountPrice()
    {
        $time= carbon::now();
        $item_ids=DB::table('discount_item')->where('finish_date','<' ,$time)->pluck('item_id'); 
        DB::table('discount_item')->where('finish_date','<' ,$time)->delete();
        ProductItem::whereIn('id',$item_ids)->update(['item_discount_price' => null]);
    }

    public static function supplierLastDiscountItems($products_ids)
    {
        $discountItems=ProductItem::with(['CriteriaBase','images',
                    'product'=>function ($q){
                         $q->with('brand:id,brand_name,brand_logo','category:id,category_name')
                         ->get();}])
                         ->whereIn('product_base_id',$products_ids)
                        ->whereNotNull('item_discount_price')
                        ->orderBy('created_at','DESC')->take(10)->get();
        return $discountItems;
    }
    public static function supplierLastAddedItems($products_ids)
    {
        $lastAddedItems= ProductItem::with(['CriteriaBase','images',
                        'product'=>function ($q) {
                            $q->with('brand:id,brand_name,brand_logo','category:id,category_name')
                            ->get();}])
                            ->whereIn('product_base_id',$products_ids)
                            ->orderBy('created_at','DESC')->take(10)->get();
        return $lastAddedItems;
    }

    public static function supplierBestSeller($supplier){
        $items_id = productItem::whereHas('Product',function($q) use($supplier)
        {$q->where('supplier_id',$supplier->id);})
        ->whereHas('orders')
        ->pluck('id');
        $bestItems = DB::table('item_order')->whereIn('item_id',$items_id)->groupBy('item_id') 
        ->orderBy(DB::raw('SUM(quantity)'), 'DESC')->take(10)->pluck('item_id');
        if (!$bestItems->isEmpty()) {
        foreach ($bestItems as  $items) {
        $item = ProductItem::with(['CriteriaBase','images',
                'product'=>function ($q){
                    $q->with('brand:id,brand_name,brand_logo','category:id,category_name')
                    ->get();}])->find($items);
        $bestSeller[] = $item;
        }
        } else { $bestSeller = [];}
        return $bestSeller;
    }

}
