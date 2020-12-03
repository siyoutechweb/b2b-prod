<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\CriteriaBase;
use App\Models\ProductBase;
use App\Models\ProductImage;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Object_;
use stdClass;

class ProductBasesController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function addProductBase(Request $request)
    {
        // $validator = Validator::make($request->all(), 
        // [ 'product_name' => 'required', 
        //   'taxe_rate' => 'required|numeric',
        //   'category_id' => 'required',
        //   'brand_id' => 'required',
        //   'product_image_url' => 'required|url',
        //   'items' => 'required',
        //   'items.*.item_barcode' => 'required|integer|digits:13',
        //   'items.*.item_box' => 'required|integer',
        //   'items.*.item_package' => 'required|integer',
        //   'items.*.item_offline_price' => 'required|numeric',
        //   'items.*.item_online_price' => 'required|numeric',
        //   'items.*.item_quantity' => 'required|integer|min:1|gte:items.*.item_warn_quantity',
        //   'items.*.item_warn_quantity' => 'integer|min:0|lte:items.*.item_quantity',
        //   'items.*.image_list' => 'required',
        //   'items.*.criteria_list' => 'required',
        //   'items.*.criteria_list.*.criteria_id' => 'required|integer|min:1',
        //   'items.*.criteria_list.*.criteria_value' => 'required|string',
        //   'items.*.criteria_list.*.criteria_unit_id' => 'required|integer|min:1'
        //   ]);
        // if ($validator->fails()) {
        //     return $validator->errors();
        // }
        $supplier = AuthController::me();
        $product_base = new ProductBase();
        $product_base->product_name = $request->input('product_name');
        $product_base->product_description = $request->input('product_description');
        $product_base->taxe_rate = $request->input('taxe_rate');
        // $product_base->product_package = $request->input('product_package');
        // $product_base->product_box = $request->input('product_box');
        $product_base->supplier_id = $supplier->id;
        $product_base->category_id = $request->input('category_id');
        $product_base->brand_id = $request->input('brand_id');
        $product_base->product_image_url = $request->input('product_image_url');
        $product_items = $request->input('items');
        
        if ($product_base->save()) {
            foreach ($product_items as $items) {
                $item = (object) $items;
                $product_item = new ProductItem();
                $product_item->item_offline_price = isset($item->item_offline_price) ? $item->item_offline_price : null;
                $product_item->item_online_price = isset($item->item_online_price) ? $item->item_online_price : null;
                $product_item->item_package = isset($item->item_package) ? $item->item_package : 1;
                $product_item->item_box = isset($item->item_box) ? $item->item_box : 1;
                $product_item->item_barcode = $item->item_barcode;
                $product_item->item_quantity = $item->item_quantity;
                $product_item->item_warn_quantity = isset($item->item_warn_quantity) ? $item->item_warn_quantity : null;
                // $product_item->item_discount_type = isset($item->item_discount_type) ? $item->item_discount_type : null;
                // $product_item->item_discount_price = isset($item->item_discount_price) ? $item->item_discount_price : null;
                $product_item->product_base_id = $product_base->id;
                $criteria_list = $item->criteria_list;
                $item_images = $item->image_list;
                if ($product_item->save()) {
                    foreach ($criteria_list as $criteriaItems) {
                        $criteriaItem = (object) $criteriaItems;
                        $criteria = CriteriaBase::find($criteriaItem->criteria_id);
                        $product_item->CriteriaBase()->attach($criteria, ["criteria_value" => $criteriaItem->criteria_value, "criteria_unit_id" => $criteriaItem->criteria_unit_id]);
                    }
                    
                    foreach ($item_images as $key => $image_id) {
                        $item_image = ProductImage::find($image_id);
                        $item_image->product_item_id = $product_item->id;
                        $item_image->save();
                    }
                    
                }
            };
            return response()->json(["msg" => "Product added Succeffully "], 200);
        }
        return response()->json(["msg" => "Error"], 404);
    }

    public function updateProductBase(Request $request, $id)
    {
        $supplier = AuthController::me();
        $product_base = ProductBase::find($id);
        $product_base->product_name = $request->input('product_name');
        $product_base->product_description = $request->input('product_description');
        $product_base->taxe_rate = $request->input('taxe_rate');
        $product_base->category_id = $request->input('category_id');
        $product_base->brand_id = $request->input('brand_id');
        $product_base->product_image_url = $request->input('img_url');
        $product_base->save();
        return response()->json(['msg'=>"product base has been updated !!"],200);
    }
    public function updateProductWithItem(Request $request)
    {
        $supplier = AuthController::me();
        $product_base_id = $request->input('product_base_id');
        $product_base = ProductBase::find($product_base_id);
        $product_base->product_name = $request->input('product_name');
        $product_base->product_description = $request->input('product_description');
        $product_base->taxe_rate = $request->input('taxe_rate');
        // $product_base->supplier_id = $supplier->id;
        // $product_base->category_id = $request->input('category_id');
        $product_base->brand_id = $request->input('brand_id');
        $product_items = $request->input('product_items');
        if ($product_base->save()) {
            foreach ($product_items as $items) {
                $sync_array = array();
                $item = (object) $items;
                $product_item = ProductItem::find($item->item_id);
                $product_item->item_offline_price = isset($item->item_offline_price) ? $item->item_offline_price : null;
                $product_item->item_online_price = isset($item->item_online_price) ? $item->item_online_price : null;
                $product_item->item_package = isset($item->item_package) ? $item->item_package : 1;
                $product_item->item_box = isset($item->item_box) ? $item->item_box : 1;
                // $product_item->item_barcode = $item->item_barcode;
                $product_item->item_quantity = $item->item_quantity;
                $product_item->item_warn_quantity = isset($item->item_warn_quantity) ? $item->item_warn_quantity : null;
                $product_item->item_discount_type = isset($item->item_discount_type) ? $item->item_discount_type : null;
                $product_item->item_discount_price = isset($item->item_discount_price) ? $item->item_discount_price : null;
                // $product_item->product_base_id = $product_base->id;
                $criteria_list = $item->criteria_list;
                //$item_images = $item->image_list;
                if ($product_item->save()) {
                    foreach ($criteria_list as $criteriaItems) {
                        $criteriaItem = (object) $criteriaItems;
                        $sync_array[$criteriaItem->criteria_id] = ["criteria_value" => $criteriaItem->criteria_value, "criteria_unit_id" => $criteriaItem->criteria_unit_id ] ; 
                    }
                    $product_item->CriteriaBase()->sync($sync_array);
                }
                else {
                    return response()->json(["msg" => "Error while saving"], 404);
                }
                
            };
            return response()->json(["msg" => "Product updated Succeffully "], 200);
        }
        else {
            return response()->json(["msg" => "Error while saving"], 404);
        }
    }

    public function deleteProductBase(Request $request, $product_id)
    {
        // $product_base_id = $request->input('product_id');
        $product = ProductBase::with('items')->find($product_id);
        foreach ($product->items as $item) 
        {
            $item->Images()->delete();
            $item->CriteriaBase()->detach();
        }
        $product->items()->delete();
        $product->wish_list()->detach();
        if ($product->delete()) {
            return response()->json(["msg" => "Product has been deleted"], 200);
        } 
        return response()->json(["msg" => "Error"], 500);
        

    }

    public function getProductList(Request $request)
    {
        $supplier = AuthController::me();
        $barcode = $request->input('barcode');
        $keyWord = $request->input('keyword');
        $category = $request->input('category_id');
        $brand = $request->input('brand_id');
        $productList= ProductBase::with(['brand','category','items'=>function ($query) use ($barcode)
        {$query->with('CriteriaBase','images')
               ->when($barcode != '', function ($q) use ($barcode)
               {$q->where('item_barcode',$barcode);})
               ->get();}])
        ->when($barcode != '', function ($q) use ($barcode){
            $q->whereHas('items',function ($q) use ($barcode)
            {$q->where('item_barcode',$barcode);});})
        ->when($keyWord != '', function ($q) use ($keyWord)
        {   $q->where('product_name', 'like', '%' . $keyWord . '%')->get();
        })
        ->when($category != '', function ($q) use ($category)
        {   $q->where('category_id', $category)->get();
        })
        ->when($brand != '', function ($q) use ($brand)
        {   $q->where('brand_id', $brand)->get();
        })
        ->where('supplier_id',$supplier->id)
        ->orderBy('id','desc')
        ->paginate(50);

        return response()->json($productList, 200);
    }

    public function getProduct(Request $request)
    {
        $supplier = AuthController::me();
        $product_id = $request->input('product_id');
        $product= ProductBase::with(['brand','category','supplier'])
        ->where('id',$product_id)->first();
        $items = ProductItem::with('images','CriteriaBase')
                            ->with(['product' => function ($q) {
                                $q->with('supplier')->get();
                            }])
                             ->where('product_base_id',$product_id)->get();
        foreach ($items as $key => $item) {
            $item->product_base_name = $product->product_name; 
            $item->supplier_id = $product->supplier_id; 
        }
        $product->items = $items;
        return response()->json($product, 200);
    }

    public function getSalesmanagerProducts(Request $request) 
    {
        $salesmanager = AuthController::me();
        $brand = $request->input('brand_id');
        $category = $request->input('category_id');
        $supplier = $request->input('supplier_id');
        $suppliersIds = $salesmanager->suppliers()->pluck('users.id');
        $productList = ProductBase::with(['category','brand','supplier'])
        ->when($brand != '', function ($query) use ($brand) {
            $query->where('brand_id', $brand);
        })
        ->when($category != '', function ($query) use ($category) {
            $query->where('category_id', $category);
        })
        ->when($supplier != '', function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier);
        })
        ->when($supplier == '', function ($query) use ($suppliersIds) {
            $query->whereIn('supplier_id', $suppliersIds);
        })
        ->orderBy('id', 'DESC')
        ->paginate(50);
        return response()->json($productList, 200);
    }

    public function getSupplierCategories()
    {
        $supplier = AuthController::me();
        $categories = $supplier->Categories()->get();
        return $categories;
    }

    public function getLastAdded(Request $request)
    {
        $supplier_id = $request->input('supplier_id');
        $category = $request->input('category_id');
        $productList = ProductItem::with(['images','CriteriaBase',
                        'product'=>function ($query)
                        {$query->with('brand:id,brand_name','category:id,category_name')->get();}])
                        ->whereHas('product', function ($query) use ($supplier_id)
                        {$query->where('supplier_id',$supplier_id);
                        })->when($category != '', function ($query) use ($category) {
                            $query->whereHas('product', function ($query) use ($category)
                            {$query->where('category_id',$category);
                            });
                        })->orderBy('id','DESC')->take(20)->get();
        return response()->json(["last_added"=> $productList], 200);
    }   
}
