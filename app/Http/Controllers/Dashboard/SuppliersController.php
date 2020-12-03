<?php namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\CriteriaBase;
use App\Models\ProductBase;
use App\Models\ProductImage;
use App\Models\ProductItem;
use App\Models\item_criteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller {

    public function bestSeller()
    {
        $supplier = AuthController::me();
        $bestSeller = ProductItem::supplierBestSeller($supplier);
        return response()->json($bestSeller, 200);
    }

    public function lastAdded(){
        $supplier = AuthController::me();
        $products_ids= $supplier->product_list()->pluck('id');
        $lastAdded  = ProductItem::supplierLastAddedItems($products_ids);
        return response()->json($lastAdded, 200);
    }

    public function productDiscount(){
        $supplier = AuthController::me();
        $products_ids= $supplier->product_list()->pluck('id');
        $discount = ProductItem::supplierLastDiscountItems($products_ids);
        return response()->json($discount, 200);
    }

    public function getShopsList(Request $request)
    {
        $supplier = AuthController::me();
        $shops = $supplier->getShopsThroughOrder()->distinct()->get();
        return response()->json(["shops"=>$shops], 200);
    }


    // API for mobile

    public function MobileHomePage(){
        $supplier = AuthController::me();
        $products_ids= $supplier->product_list()->pluck('id');
        $data['lastAdded'] = ProductItem::supplierLastAddedItems($products_ids);
        $data['discount'] = ProductItem::supplierLastDiscountItems($products_ids);
        $data['bestSeller'] = ProductItem::supplierBestSeller($supplier);
        return response()->json($data, 200);
    }

}
