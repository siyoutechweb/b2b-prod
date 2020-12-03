<?php namespace App\Http\Controllers\Product;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductItem;
use App\Models\User;
use App\Models\Discount;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiscountsController extends Controller {

    public function addPromotion(Request $request)
    {
        ProductItem::updateDiscountPrice();
        $discount_id = $request->input('discount_id');
        $discount= Discount::find($discount_id);
        $items = $request->input('items_id');
        $start_date = $request->input('start_date');
        $finish_date = $request->input('finish_date');
        $value = $request->input('value');
        if ($discount_id == 1)
        {
            foreach ($items as $key => $item_id) 
            {
                $item = productItem::find($item_id);
                $discount_price=$item->item_offline_price-($item->item_offline_price*(float)$value)/100;
                $item->Discount()->attach($discount, 
                ['discount_value'=> $value,
                'start_date'=> $start_date,
                'finish_date'=> $finish_date]);
                $item->item_discount_price=$discount_price;
                $item->save();
            }
        }
        elseif ($discount_id == 2)
        {
            foreach ($items as $key => $item_id) 
            {
                $item= productItem::find($item_id);
                $discount_price = $item->item_offline_price-(float)$value;
                $item->Discount()->attach($discount, 
                ['discount_value'=> $value,
                'start_date'=> $start_date,
                'finish_date'=> $finish_date,
                ]);
                $item->item_discount_price=$discount_price;
                $item->save();
            }
        }
        elseif ($discount_id == 3)
        {
            foreach ($items as $key => $item_id) 
            {
                $item= productItem::find($item_id);
                $discount_price = $value;
                $item->Discount()->attach($discount, 
                ['discount_value'=> $value,
                'start_date'=> $start_date,
                'finish_date'=> $finish_date]);
                $item->item_discount_price=$discount_price;
                $item->save();
            }
        }
        else  { return response()->json(["msg"=> "Error !!"],500); }
        return response()->json(["msg"=> "Promotion has been saved !!"],200);    
    }

    public function getDiscountItem(Request $request)
    {
        ProductItem::updateDiscountPrice();
        $supplier_id = $request->input('supplier_id');
        $category = $request->input('category_id');
        $brand = $request->input('brand_id');
        $keyword = $request->input('keyword');
        $barcode = $request->input('barcode');
        $discountItems=ProductItem::with(['discount','images','CriteriaBase',
        'product'=>function ($query)
        {$query->with('brand:id,brand_name','category:id,category_name','supplier')->get();}])
        ->whereHas('product', function ($query) use ($supplier_id)
        {$query->where('supplier_id',$supplier_id);
        })->when($category != '', function ($query) use ($category) {
            $query->whereHas('product', function ($query) use ($category)
            {$query->where('category_id',$category);
            });
        })
        ->when($brand != '', function ($query) use ($brand) {
            $query->whereHas('product', function ($query) use ($brand)
            {$query->where('brand_id',$brand);
            });
        })
        ->when($keyword != '', function ($q) use ($keyword)
                        {  $q->whereHas('product',function ($q) use ($keyword){
                            $q->where('product_name', 'like', '%' . $keyword . '%')
                            // ->orWhere('product_description', 'like', '%' . $keyWord . '%')
                            ;});   
                        })
        ->when($barcode != '',  function ($q) use ($barcode)
                        {$q->where('item_barcode',$barcode)->get();})
        ->whereNotNull('item_discount_price')->orderBy('id','DESC')->get();

        return response()->json(["Discount"=>$discountItems]); 
    }

    public function getDiscountList(Request $request)
    {
        $types = Discount::all();
        return response()->json($types);
    } 

    public function getSupplierDiscountItems(Request $request)
    {
        ProductItem::updateDiscountPrice();
        $category = $request->input('category_id');
        $brand = $request->input('brand_id');
        $keyword = $request->input('keyword');
        $barcode = $request->input('barcode');
        $supplier = AuthController::me();
        $products_ids= $supplier->product_list()->pluck('id');
        $items = ProductItem::with(['CriteriaBase','images','Discount',
                    'product'=>function ($q){
                         $q->with('brand:id,brand_name,brand_logo','category:id,category_name')
                           ->get();}])
                           ->when($category != '', function ($query) use ($category) {
                            $query->whereHas('product', function ($query) use ($category)
                            {$query->where('category_id',$category);
                            });
                        })
                        ->when($brand != '', function ($query) use ($brand) {
                            $query->whereHas('product', function ($query) use ($brand)
                            {$query->where('brand_id',$brand);
                            });
                        })
                        ->when($keyword != '', function ($q) use ($keyword)
                                        {  $q->whereHas('product',function ($q) use ($keyword){
                                            $q->where('product_name', 'like', '%' . $keyword . '%')
                                            // ->orWhere('product_description', 'like', '%' . $keyWord . '%')
                                            ;});   
                                        })
                        ->when($barcode != '',  function ($q) use ($barcode)
                                        {$q->where('item_barcode',$barcode)->get();})
                        ->whereIn('product_base_id',$products_ids)
                        ->whereNotNull('item_discount_price')
                        ->orderBy('created_at','DESC')
                        ->paginate(10);
        return response()->json($items, 200);
    } 

}
