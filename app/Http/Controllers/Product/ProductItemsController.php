<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CriteriaBase;
use App\Models\ProductBase;
use App\Models\ProductImage;
use App\Models\ProductItem;
use App\Models\item_criteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Object_;
use stdClass;

class ProductItemsController extends Controller
{

    public function addItem(Request $request)
    {
        $product_base_id = $request->input('product_base_id');
        $product_item = new ProductItem();
        $product_item->item_offline_price = $request->input('item_offline_price');
        $product_item->item_online_price = $request->input('item_online_price');
        $product_item->item_package = $request->input('item_package');
        $product_item->item_box = $request->input('item_box');
        $product_item->item_barcode = $request->input('item_barcode');
        $product_item->item_quantity = $request->input('item_quantity');
        $product_item->item_warn_quantity = $request->input('item_warn_quantity');
        $product_item->item_discount_type = $request->input('item_discount_type');
        $product_item->item_discount_price = $request->input('item_discount_price');
        $product_item->product_base_id = $product_base_id;
        $criteria_list = $request->input('criteria_list');
        if ($product_item->save()) {
            foreach ($criteria_list as $criteriaItems) {
                $criteriaItem = (object) $criteriaItems;
                $criteria = CriteriaBase::find($criteriaItem->criteria_id);
                $product_item->CriteriaBase()->attach($criteria, ["criteria_value" => $criteriaItem->criteria_value, "criteria_unit_id" => $criteriaItem->criteria_unit_id]);
            }
            if ($request->filled('item_images')) {
                $item_images = $request->input('item_images');
                foreach ($item_images as $key => $image_id) {
                    $item_image = ProductImage::find($image_id);
                    $item_image->product_item_id = $product_item->id;
                    $item_image->save();
                }
            }
        }
        return response()->json(["msg" => "Item added Succeffully "], 200);
    }

    public function addItemCriteria(Request $request)
    {
        $supplier = AuthController::me();
        $item_id = $request->input('item_id');
        $criteria_list = $request->input('criteria_list');
        $product_item = ProductItem::find($item_id);
        foreach ($criteria_list as $criteriaItems) {
            $criteriaItem = (object) $criteriaItems;
            $criteria = CriteriaBase::find($criteriaItem->criteria_id);
            $product_item->CriteriaBase()->attach($criteria, ["criteria_value" => $criteriaItem->criteria_value, "criteria_unit_id" => $criteriaItem->criteria_unit_id]);
        }
        return response()->json(["msg" => "Cretiria added Succeffully "], 200);
    }


    public function updateItem(Request $request, $item_id)
    {
        $sync_array = array();
        $product_item = ProductItem::find($item_id);
        $product_item->item_offline_price = $request->input('item_offline_price');
        $product_item->item_online_price = $request->input('item_online_price');
        $product_item->item_package = $request->input('item_package');
        $product_item->item_box = $request->input('item_box');
        $product_item->item_barcode = $request->input('item_barcode');
        $product_item->item_quantity = $request->input('item_quantity');
        $product_item->item_warn_quantity = $request->input('item_warn_quantity');
        $product_item->item_discount_type = $request->input('item_discount_type');
        $product_item->item_discount_price = $request->input('item_discount_price');
        // $product_item->product_base_id = $product_base->id;
        $product_item->save();
        return response()->json(["msg" => "Success"], 200);
        // $criteria_list = $request->input('criteria_list');
        // if ($request->filled('item_images')) {
        //     $item_images = $request->input('item_images');
        //     foreach ($item_images as $key => $image_id) {
        //         $item_image = ProductImage::find($image_id);
        //         $item_image->product_item_id = $product_item->id;
        //         $item_image->save();
        //     }
        // }
    
        // if ($product_item->save()) {

        //     foreach ($criteria_list as $criteriaItems) {
        //         $criteriaItem = (object) $criteriaItems;
        //         $sync_array[$criteriaItem->criteria_id] = ["criteria_value" => $criteriaItem->criteria_value, "criteria_unit_id" => $criteriaItem->criteria_unit_id];
        //     }
        //     $product_item->CriteriaBase()->sync($sync_array);
        //     return response()->json(["msg" => "Success"], 200);
        // } else {
        //     return response()->json(["msg" => "Error while saving"], 404);
        // }
    }

    public function updateItemCriteria(Request $request)
    {
        $item_id = $request->input('item_id');
        $criteria_id = $request->input('criteria_id');
        $criteria_value = $request->input('criteria_value');
        $item = ProductItem::find($item_id);
        $item->CriteriaBase()->updateExistingPivot($criteria_id, ["criteria_value" => $criteria_value]);
        return response()->json(["msg" => "Success"], 200);
    }


    public function deleteItem(Request $request, $item_id)
    {
        // $item_id = $request->input('item_id');
        $item = ProductItem::find($item_id);
        $item->Images()->delete();
        $item->CriteriaBase()->detach();
        if ($item->delete()) {
            return response()->json(["msg" => "Item has been deleted"], 200);
        }
        return response()->json(["msg" => "Error"], 500);
    }

    public function deleteItemCriteria(Request $request)
    {
        $item_id = $request->input('item_id');
        $criteria_id = $request->input('criteria_id');
        $item = ProductItem::find($item_id);
        if ($item->CriteriaBase()->detach($criteria_id)) {
            return response()->json(["msg" => "Item criteria has been deleted"], 200);
        }
        return response()->json(["msg" => "Error"], 500);
    }

    public function getItem(Request $request, $item_id)
    {
        $supplier = AuthController::me();
        // $item_id = $request->input('item_id');
        $item = ProductItem::with(['CriteriaBase','images','product'=>function ($q)
        {$q->with('brand:id,brand_name','category:id,category_name','supplier')
               ->get();}])->find($item_id);
        return response()->json($item);
    }

    public function getProductItemList(Request $request)
    {
        $supplier = AuthController::me();
        $product_id = $request->input('product_id');
        $itemList = ProductBase::with(['brand', 'category', 'items' => function ($query) {
            $query->with('CriteriaBase', 'images')->get();
        }])
            ->find($product_id);
        return response()->json($itemList);
    }

    public function searchItem(Request $request)
    {
        $key_word = $request->input('key_word');
        if (is_numeric($key_word)) {
            $items = ProductItem::where('item_barcode', $key_word)->get();
            if (!$items->isEmpty()) {
                $product_base = $items[0]->Product()->first();
                $product_base->items = $items;
                return response()->json([$product_base], 200);
            }
            return response()->json(["msg" => "Item not found "]);
        } else {
            $product_base = ProductBase::with('items')->where('product_name', $key_word)->get();
            return response()->json($product_base, 200);
        }
    }
    // public function uploadImage(Request $request)
    // {
    //     if ($request->hasFile('product_item_image')) {
    //         $path = $request->file('product_item_image')->store('products', 'google');
    //         $fileUrl = Storage::url($path);
    //         $image = DB::table('product_images')->insert([
    //             "image_url" => $fileUrl
    //         ]);
    //         return response()->json(["id" => $image->id, "image_url" => $fileUrl], 200);
    //     }
    //     return response()->json('Error', 500);
    // }

    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('products', 'google');
            $fileUrl = Storage::url($path);
            $image = new ProductImage();
            $image->image_url = $fileUrl;
            $image->image_name = basename($path);
            $image->save();
            return response()->json(["id" => $image->id, "image_url" => $fileUrl], 200);
        }
        return response()->json(['msg' => 'Error'], 500);
    }

    public function deleteImage($id)
    {
        $image = ProductImage::find($id);
        $isImageDeleted = Storage::disk('google')->delete('products/' . $image->image_name);
        if ($isImageDeleted) {
            $image->delete();
            return response()->json(['msg' => 'Image Deleted'], 200);
        }
        return response()->json(['msg' => 'Error'], 500);
    }

    public function getSupplierItems(Request $request)
    {
        $supplier = AuthController::me();
        $barcode = $request->input('barcode');
        $keyWord = $request->input('keyword');
        $products_ids= $supplier->product_list()->pluck('id');
        $items = ProductItem::with(['CriteriaBase','images',
                    'product'=>function ($q)use ($keyWord){
                         $q->with('brand:id,brand_name,brand_logo','category:id,category_name')  
                        ->get();}])
                        ->whereIn('product_base_id',$products_ids)
                        ->whereNull('item_discount_price')
                        ->when($barcode != '',  function ($q) use ($barcode)
                        {$q->where('item_barcode',$barcode)->get();})
                        ->when($keyWord != '', function ($q) use ($keyWord)
                        {  $q->whereHas('product',function ($q) use ($keyWord){
                            $q->where('product_name', 'like', '%' . $keyWord . '%')
                            // ->orWhere('product_description', 'like', '%' . $keyWord . '%')
                            ;});   
                        })
                        ->orderBy('created_at','DESC')->paginate(10);
        return response()->json($items, 200);
    }



    /* generate EAN13 Barcode API
     - Necessary Parameters: 'token','chain_id'
     - optional Parameters: 
    */
    public function generateBarcode(Request $request)
    {
        // $validator = Validator::make($request->all(), 
        // [ 'product_name' => 'required',
        $supplier = AuthController::me();
        $new_barcode = $this->newBarcode();

        return response()->json($new_barcode); 

    }

    public static function newBarcode()
    {
        $number = rand(pow(10, 7) - 1, pow(10, 8) - 1);
        $barcode = str_split($number);
        $sum=0;
        foreach ($barcode as $key => $num)
        {
            if($key % 2 == 0)  {$sum=$sum+$num;}
            else{ $sum=$sum+$num*3;}
        }

        if(($sum+2) % 10 == 0 ){ array_push($barcode,0);}
        else { array_push($barcode,10-(($sum+2) % 10));}
        array_unshift($barcode, "2","0","0","0");

        $new_barcode = implode('', $barcode);
        return $new_barcode;
    }

}
