<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProductBase;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowProductsController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function getShopProducts(Request $request)
    {
        $shop = AuthController::me();
        $brand = $request->input('brand_id');
        $category = $request->input('category_id');
        $supplier = $request->input('supplier_id');
        $productList = ProductBase::with(['supplier', 'brand', 'category'])
            ->when($brand != '', function ($query) use ($brand) {
                $query->where('brand_id', $brand);
            })
            ->when($category != '', function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($supplier != '', function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier);
            })
            ->when($supplier == '', function ($query) use ($shop) {
                $query->whereIn('supplier_id', $this->supplierIds($shop));
            })
            ->orderBy('id', 'DESC')
            ->paginate(50);
        $this->inWishList($productList, $shop);
        return response()->json($productList, 200);
    }

    public function getShopProductList(Request $request)
    {
        $shop = AuthController::me();
        $brand = $request->input('brand_id');
        $category = $request->input('category_id');
        $supplier = $request->input('supplier_id');
        $barcode = $request->input('barcode');
        $productList = ProductBase::with(['supplier', 'brand', 'category',
                        'items'=> function ($q){$q->with(['CriteriaBase','images',])->get();}])
            ->when($brand != '', function ($query) use ($brand) {
                $query->where('brand_id', $brand);
            })
            ->when($category != '', function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($supplier != '', function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier);
            })
            ->when($supplier == '', function ($query) use ($shop) {
                $query->whereIn('supplier_id', $this->supplierIds($shop));
            })
            ->when($barcode != '', function ($q) use ($barcode){
                $q->whereHas('items',function ($q) use ($barcode)
                {$q->where('item_barcode',$barcode);});})
            ->orderBy('id', 'DESC')
            ->paginate(50);
        $this->inWishList($productList, $shop);
        return response()->json($productList, 200);
    }


    public function getSuppliersList(Request $request)
    {
        $shop = AuthController::me();
        $suppliers = $shop->supplier()->get();
        $public_supplier = user::where('product_visibility', '=', true)
            // ->orWhereHas('ProductBase', function ($query) {
            //     $query->WhereHas('items', function ($query) {
            //         $query->where('item_online_price', '!=', null);
            //     });})
            ->get();
        $supplierList['suppliers'] = $suppliers->merge($public_supplier);

        return response()->json($supplierList, 200);
    }



    private function SupplierIds($shop)
    {

        $suppliers = $shop->supplier()->pluck('users.id');
        $public_supplier = user::where('product_visibility', '=', true)
            ->pluck('id');
        $supplierIds = $suppliers->merge($public_supplier);

        return $supplierIds;
    }

    private function inWishList($productList, $shop)
    {

        foreach ($productList as $product) {
            if ($shop->favorit_product()->where('product_base_id', $product->id)->exists()) {
                $product->wish_list = true;
            } else  $product->wish_list = false;
        }
    }

    public function HomePage(Request $request)
    {
        $shop = AuthController::me();
        $lastAdded = ProductBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
                    ->whereIn('supplier_id',$this->SupplierIds($shop))
                    ->orderBy('id','DESC')->take(5)->get();
        $lastOrder= $shop->shopOrders()->with('productItem')->orderBy('created_at','DESC')->first();
        if(!empty($lastOrder))
        {
            foreach ($lastOrder->productItem as $key => $item) {
                $categories[] = $item->Product->category_id;
                if ($key == 5) {
                    break;
                }
            }
            $recommended = ProductBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
                ->WhereIn('supplier_id', $this->SupplierIds($shop))
                ->whereIn('category_id', $categories)
                ->inRandomOrder()->take(5)->get();
        } else {
            $recommended = ProductBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
                ->WhereIn('supplier_id', $this->SupplierIds($shop))
                ->inRandomOrder()->take(5)->get();
        }

        $bestItems = DB::table('item_order')->groupBy('item_id') 
        ->orderBy(DB::raw('SUM(quantity)'), 'DESC')->take(5)->pluck('item_id');
        
        if (!$bestItems->isEmpty()) {
            foreach ($bestItems as $key => $items) {
                $item = ProductItem::with('images')->find($items);
                $product = productBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
                    ->find($item->product_base_id);
                $product->item = $item;
                $bestSeller[] = $product;
            }
        } else { $bestSeller = [];}
        
        $products= ProductBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
            ->with('item:product_base_id,item_online_price')->WhereIn('supplier_id', $this->SupplierIds($shop))
            ->inRandomOrder()->take(20)->get();
         
        $this->inWishList($bestSeller,$shop);
        $this->inWishList($recommended,$shop);
        $this->inWishList($lastAdded,$shop); 
        $this->inWishList($products,$shop); 
        $data['last_added'] = $lastAdded;
        $data['recommended'] = $recommended;
        $data['bestSeller'] = $bestSeller;
        $data['products'] = $products;
        return $data;
        return response()->json($data, 200);
    }

    public function supplierBestSeller($supplier_id)
    {
        
        $items_id = productItem::whereHas('Product',function($q) use($supplier_id)
                        {$q->where('supplier_id',$supplier_id);})
                        ->whereHas('orders')
                        ->pluck('id');
        $bestItems = DB::table('item_order')->whereIn('item_id',$items_id)->groupBy('item_id') 
        ->orderBy(DB::raw('SUM(quantity)'), 'DESC')->take(5)->pluck('item_id');
       
        if (!$bestItems->isEmpty()) {
            foreach ($bestItems as  $items) {
                $item = ProductItem::with('images')->find($items);
                $product = productBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
                    ->find($item->product_base_id);
                $item ->product_base = $product;
                $bestSeller[] = $item;
            }
            // $product = productBase::with(['supplier:id,first_name,last_name', 'brand:id,brand_name', 'category:id,category_name'])
            //         ->with('items', function ($q){$q->where('id',$bestItems);})->get();

        } else { $bestSeller = [];}
        
        $data['bestSeller'] = $bestSeller;
        
        return response()->json($data, 200);
    }

    public function purchasedListBySupplier(Request $request)
    {
        $shop = AuthController::me();
        $supplier_id= $request->input('supplier_id');
        $category=$request->input('category_id');
        $purchasedList=ProductItem::with(['images','CriteriaBase',
                        'product'=>function ($q)
                        {$q->with('brand:id,brand_name','category:id,category_name','supplier:id,first_name,last_name')
                               ->get();}])
                        ->wherehas('orders',function($q) use($shop ,$supplier_id)
                            {$q->where(['shop_owner_id'=>$shop->id,
                            'supplier_id'=>$supplier_id]);})
                        ->when($category != '', function ($q) use ($category) {
                                $q->whereHas('product', function ($q) use ($category)
                                {$q->where('category_id',$category);});})
                        ->get();
        return response()->json(["purchased"=>$purchasedList], 200);
    }

    public function purchasedList(Request $request)
    {
        $shop = AuthController::me();
        $supplier_id= $request->input('supplier_id');
        $category_id= $request->input('category_id');
        $brand_id= $request->input('brand_id');
        $barcode =$request->input('barcode');
        $purchasedList=ProductItem::with(['images','CriteriaBase',
                        'product'=>function ($q)
                        {$q->with('brand:id,brand_name','category:id,category_name','supplier:id,first_name,last_name')
                               ->get();}])
                        ->wherehas('orders',function($q) use($shop ,$supplier_id)
                            {$q->where('shop_owner_id', $shop->id);})
                        ->when($barcode != '', function ($query) use ($barcode) {
                            $query->where('item_barcode',$barcode);})
                        ->when($supplier_id != '', function ($q) use ($supplier_id)
                        { $q->whereHas('product',function ($q) use ($supplier_id)
                            {$q->where('supplier_id',$supplier_id);});})
                        ->when($brand_id != '', function ($q) use ($brand_id)
                        { $q->whereHas('product',function ($q) use ($brand_id)
                            {$q->where('brand_id',$brand_id);});})
                        ->when($category_id != '', function ($q) use ($category_id)
                            {$q->whereHas('product',function ($q) use ($category_id)
                            {$q->where('category_id',$category_id);});})
                        ->get();
        return response()->json($purchasedList, 200);
    }

    public function searchItemByBarcode(Request $request)
    {
        $barcode= $request->input('barcode');
        $category=$request->input('category_id');
        $items=ProductItem::with(['images','CriteriaBase',
                        'product'=>function ($q)
                        {$q->with('brand:id,brand_name','category:id,category_name','supplier')
                               ->get();}])
                        ->where('item_barcode',$barcode)
                        ->get();
        return response()->json(["items"=>$items], 200);
    }

    public function supplierOverview(Request $request, $id)
    {
        $supplier=User::whereHas('role', function ($query) {
            $query->where('name', '=', 'Supplier');})->find($id);
        if(isset($supplier))
        {
            $products_ids= $supplier->product_list()->pluck('id');
            $data['supplier'] = $supplier;
            $data['lastAdded'] = ProductItem::supplierLastAddedItems($products_ids);
            $data['discount'] = ProductItem::supplierLastDiscountItems($products_ids);
            $data['bestSeller'] = ProductItem::supplierBestSeller($supplier);
            return response()->json($data, 200);
        }
        return response()->json(["msg"=>"supplier not found"]);
    }
}
