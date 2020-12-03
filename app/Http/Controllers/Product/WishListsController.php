<?php namespace App\Http\Controllers\Product;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class WishListsController extends Controller {

    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function wishList(Request $request)
    {
        $user = AuthController::me();
        $product_id = $request->input('product_id');
        if($user->favorit_product()->where('product_base_id',$product_id)->exists())
        {
            $prodcut =$user->favorit_product()->detach($product_id);
            return response()->json(['msg'=>'product has been removed from favorit list'],200); 
        }
        else{
            $prodcut =$user->favorit_product()->attach($product_id);
            return response()->json(['msg'=>'product has been added to favorit list'],200);

        }
          
    }

    public function getWishList(Request $request)
    {
        $user = AuthController::me();
        $supplier_id = $request->input('supplier_id');
        $category_id = $request->input('category_id');
        $brand_id = $request->input('brand_id');
        $wishList = array();
        $list= $user->favorit_product()->with(['supplier','brand','category'])
                ->when($supplier_id != '', function ($query) use ($supplier_id) {
                    $query->where('supplier_id',$supplier_id);})
                ->when($category_id != '', function ($query) use ($category_id) {
                    $query->where('category_id',$category_id);})
                ->when($brand_id != '', function ($query) use ($brand_id) {
                        $query->where('brand_id',$brand_id);})
                ->get();
        $wishList ['wishList'] = $list;
        return response()->json($wishList,200);  
    }
}
