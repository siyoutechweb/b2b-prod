<?php 
namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use App\Models\Catalog;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class CatalogsController extends Controller {

    /**
 * Supplier API
 */
 public function AddCatalog(Request $request){
  $supplier = AuthController::me();

      $catalog=new Catalog();
      $catalog->catalog_name=$request->input("catalog_name");
      $catalog->catalog_description=$request->input("catalog_description");
      $catalog->catalog_start_time=$request->input("catalog_start_time");
      $catalog->catalog_end_time=$request->input("catalog_end_time");
      $catalog->user_id=$supplier->id;
      $catalog->save();
      return response()->json($catalog);

 }
 public function supplier_Cataloglist()
 {
    $supplier = AuthController::me();

     $supplier_catalogsList = Catalog::where('user_id', $supplier->id)->get();
     return response()->json($supplier_catalogsList);
    return response()->json(["msg"=>"There's No Catalog of Yours !"],500);
  
 }
 public function Supplier_showCatalog($id)
 {
  $supplier = AuthController::me();

     $catalog = Catalog::with('products')->where('user_id',$supplier->id)->where('id',$id)->get();

     return response()->json($catalog);

     return response()->json(["msg" => "ERROR !!"],500);
 }
 public function UpdateCatalog(Request $request,$id)
 {
    $supplier = AuthController::me();

     $catalog=Catalog::findorfail($id);
     if($catalog->user_id == $supplier->id){
     $catalog->catalog_name = $request->input('catalog_name');
     $catalog->catalog_description = $request->input('catalog_description');
     $catalog->catalog_start_time = $request->input('catalog_start_time');
     $catalog->catalog_end_time = $request->input('catalog_end_time');
     $product_id = $request->input('products');
   
             $products = Product::find($product_id);
             $catalog->products()->sync($products);
             $catalog->save();
         
         return response()->json("Catalog has been Updated Successfully !");
     }
     return response()->json("ERROR !!");
}
public function AddProductstocatalog(Request $request,$id){
    $supplier = AuthController::me();

    $catalog=Catalog::findorfail($id);
    $product_id=$request->input('products');
    $products=Product::find($product_id);
    $catalog->products()->sync($products);
    return response()->json(["msg"=>"products added to successfully !!"],200);
}
public function TopProductslist(){
    $supplier = AuthController::me();
    $top_products=Catalog::with('products')->where('catalog_name', 'TOP Products')->where('user_id', $supplier->id)->get();
    if(count($top_products) == 1 ){
    return response()->json($top_products);
    }
    return response()->json(["msg"=>"You Don't have Top Products Yet"],200);

}

public function DeleteCatalog($id){
    $supplier = AuthController::me();

    $catalog=Catalog::find($id);
    if($catalog->user_id == $supplier->id){
    $product_id = Product::find('products');
            $product = Product::find($product_id);
            $catalog->products()->detach($product);
            $catalog->delete();
        return response()->json("Catalog has been Deleted Successfully !");
    }
    return response()->json("ERROR !!");    
}
/**
 * other users API
 */
 public function showCatalog($id)
 {
     $catalog = Catalog::with('products')->find($id);
if($catalog->catalog_start_time == Carbon::now()){
     return response()->json($catalog);
}
     return response()->json(["msg" => "time out"],500);
 }
 public function Cataloglist()
 {
     $catalogsList = Catalog::with('products')->get();
     return response()->json($catalogsList);
 }

 public function getCatalogsBysupplier($id)
 {
     $supplier_catalogs = Catalog::with('products')->where('user_id',$id)->get();
    
     return response()->json($supplier_catalogs);
 }

}
