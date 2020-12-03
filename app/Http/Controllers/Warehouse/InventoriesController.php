<?php namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InventoryProduct;
use App\Models\Inventory;
//use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Wholesaler;
use App\Models\PurchaseProduct;
use App\Models\SupplierWarehouse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
//use mysql_xdevapi\Exception;
class InventoriesController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function generateBatchNumber()
    {
        $dt = Carbon::now();
        $date = str_replace("-","",$dt->toDateString());
        $time = str_replace(":","",$dt->toTimeString());
        $batchNumber = $date."-".$time;
        $response['code']=1;
        $response['msg']='';
        $response['data']= $batchNumber;
        return response()->json($response);
    }

    public function newInventory(Request $request)
    {
        // $operator_status = true;
	    $shop_owner = AuthController::me();
        $products =$request->input('products');
        $tmp = Inventory::where('batch_number',$request->input('batch_number'))->first();
        if($tmp) {
        return response()->json(['code'=>0,'msg'=>'batch number already exists']);
        }
        $inventory = new Inventory();
        $inventory->batch_number = $request->input('batch_number');
        $inventory->operator = $request->input('operator');
        $inventory->verifier = $request->input('verifier');
        $inventory->date = $request->input('date');
        $inventory->warehouse_id = $request->input('warehouse_id');
	$inventory->wholesaler_id = $request->input('supplier_id');
	
        $inventory->user_id = $shop_owner->id;
        $inventory->operator_status = $request->input('operator_status');
        $inventory->verifier_status = $request->input('verifier_status');
	$inventory->inventory_status = 'complete';

	$inventory_status = 'complete';
        if ($inventory->save()) {
            foreach ($products as  $inv_product) 
        {   if($inv_product['product_status'] ==2) {

		$inventory_status = 'pending';
            }
            $quantity =isset($inv_product['verified_quantity'])?$inv_product['verified_quantity']:2;
            //$value2 =$request->has('value2')? $request->input('value2'):0;
            $purchaseProduct =PurchaseProduct::where('product_barcode', $inv_product["product_barcode"])->first();
            // PurchaseProduct::find($inv_product["id"]);
          if(!$purchaseProduct) {
                $purchaseProduct = new PurchaseProduct();
                $purchaseProduct->product_name =$inv_product['product_name'];
                $purchaseProduct->product_barcode =$inv_product['product_barcode'];
                $purchaseProduct->product_description =$inv_product['product_description'];
                $purchaseProduct->product_quantity =isset($inv_product['product_quantity'])? $inv_product['product_quantity'] :0 ;
                $purchaseProduct->cost_price =isset($inv_product['cost_price'])?$inv_product['cost_price']:0.01;
                //$purchaseProduct->unit_price =isset($inv_product['unit_price'])?$inv_product['unit_price']:0.01;
                $purchaseProduct->tax_rate =isset($inv_product['tax_rate'])?$inv_product['tax_rate']:0.01;     
                $purchaseProduct->product_weight =isset($inv_product['product_weight'])?$inv_product['product_weight']:0.01;   
                $purchaseProduct->product_color =isset($inv_product['product_color'])?$inv_product['product_color']:0;      
                $purchaseProduct->supplier_id =isset($inv_product['supplier_id'])?$inv_product['supplier_id']:0;     
                $purchaseProduct->category_id =isset($inv_product['category_id'])?$inv_product['category_id']:70;     
                $purchaseProduct->user_id =$shop_owner->id;         
                $purchaseProduct->save();   
                
            }           $product =  product::where([
                    'product_barcode' => $purchaseProduct->product_barcode,
                    'supplier_id' => $purchaseProduct->user_id])->first();
            
            if (empty($product)) {
                $product_id = DB::table('products')->insertGetId(
                    [ 'product_name' =>  $purchaseProduct->product_name,
                      'product_barcode' => $purchaseProduct->product_barcode,
                      'product_description' =>  $purchaseProduct->product_description,
                      'product_price' =>  $purchaseProduct->cost_price,
                      //'unit_price' =>  $purchaseProduct->cost_price,
                      //'tax_rate' => $purchaseProduct->tax_rate,
                      'product_weight' => $purchaseProduct->product_weight,
                      'product_color' => $purchaseProduct->product_color,
                      'quantity' => $quantity,
		      'product_image'=>'https://drive.google.com/uc?id=1DMhqGEKUnnTbRowJLr7rPpvW0LzgeBzu',
                      //$inv_product['quantity'],
                      'supplier_id' => $request->input('supplier_id'),
                      'category_id' => $purchaseProduct->category_id,
                      'supplier_id' => $purchaseProduct->user_id,
                    ]
                );
                $inv_prod = DB::table('inventory_product')->insert(
                    [ 'inventory_id' => $inventory->id,
                      'product_id' => $product_id,

                      'arrived_quantity' =>  isset($inv_product['arrived_quantity'])?$inv_product['arrived_quantity']:0,
                      'verified_quantity' =>  isset($inv_product['verified_quantity'])?$inv_product['verified_quantity']:0,
                      'unverified_quantity' =>  isset($inv_product['arrived_quantity'])?$inv_product['arrived_quantity'] -$inv_product['verified_quantity'] :0,
                      'total_quantity' =>  isset($inv_product['product_quantity'])? $inv_product['product_quantity']:0,
		              'product_status'=>   isset($inv_product['product_status']) ? $inv_product['product_status']:2,
                      'created_at' =>  Carbon::now(),'updated_at' =>  Carbon::now()]);
                // if( $inv_product['quantity'] < $purchaseProduct->product_quantity)
                // {
                //     $operator_status = false;
                // }
            }
            else {
		        $product->quantity += $inv_product['verified_quantity'];
                $product->save();
                $inv_prod = DB::table('inventory_product')->insert(
                    [ 'inventory_id' => $inventory->id,
                      'product_id' => $product->id,
                      'arrived_quantity' =>  isset($inv_product['arrived_quantity'])?$inv_product['arrived_quantity']:0,
                      'verified_quantity' =>  isset($inv_product['verified_quantity'])?$inv_product['verified_quantity']:0,
                      'unverified_quantity' =>  isset($inv_product['arrived_quantity'])?$inv_product['arrived_quantity'] -$inv_product['verified_quantity'] :0,
                      'total_quantity' =>  isset($inv_product['product_quantity'])? $inv_product['product_quantity']:0,
		              'product_status'=>   $inv_product['product_status'],
                      'created_at' =>  Carbon::now(),
                      'updated_at' =>  Carbon::now()]);
                      
               
                // if( $inv_product['quantity'] < $purchaseProduct->product_quantity)
                // {
                //     $operator_status = false;
                // }
            }    
        }
         if ($inventory_status == 'pending') {
      
            $inventory->inventory_status = 'pending';
             $inventory->save();
             }  
            $response['code']=1;
            $response['msg']='';
            $response['data']="Success";
            return response()->json($response);
        } 
        else 
        {
            $response['code']=0;
            $response['msg']='';
            $response['data']= "Error while saving";
            return response()->json($response);
        }  
    }


    public function getInventories(Request $request, $warehouse_id)
    {
        //$shop_owner= AuthController::me();

        $operator_status = $request-> input('operator_status');
        $supplier = $request-> input('supplier_id');
        $batchNumber = $request-> input('batch_number');
        $verifier_status = $request-> input('verifier_status');
        $inventoryList= Inventory::with('warehouse','verifier_status:id,statut_name','operator_status:id,statut_name','wholesalers')->with('products')
        ->where('warehouse_id', $warehouse_id)
     
        ->when($operator_status != '', function ($q) use ($operator_status) 
            {$q->where('operator_status',$operator_status);})
        ->when($verifier_status != '', function ($q) use ($verifier_status) 
            {$q->where('verifier_status',$verifier_status);})
        ->when($batchNumber != '', function ($q) use ($batchNumber) 
            {$q->where('batch_number','like','%'.$batchNumber.'%');})
        ->when($supplier != '', function ($q) use ($supplier) 
            {$q->where('wholesaler_id',$supplier);})
        ->get();
        $response['code']=1;
        $response['msg']='';
        $response['data']= $inventoryList;
        return response()->json($response);
    }





    public function getInventory(Request $request, $id)
    {
        $status = $request->input('status_id');
        $inventory= Inventory::with('warehouse','operator_status','verifier_status','products')->where('id',$id)->get();
        $response['code']=1;
        $response['msg']='';
        $response['data']= $inventory;
        return response()->json($response);
    }

    public function updateInventory(Request $request, $id)
    {
        $products = $request->input('products');
        $inventory= Inventory::find($id);
        $status = true;
	$inventory_status = 'complete';
        foreach ($products as $product) {
	    $prod_id = Product::where('product_barcode',$product['product_barcode'])->first()->id;
            $inv_prod = DB::table('inventory_product')->where(['inventory_id'=> $id, 'product_id'=> $prod_id])->first();
	//var_dump($inv_prod);
            $updated_qt=$product['added_quantity'];
            $verified_quantity = $product['verified_quantity'];
            $unverified_quantity =$product['added_quantity']- $product['verified_quantity'];
            DB::table('inventory_product')->where(['inventory_id'=> $id, 'product_id'=> $prod_id])
            ->update(['arrived_quantity' => $updated_qt,
            'product_status'=>$product['product_status'],
            'verified_quantity'=>$verified_quantity,
            'unverified_quantity'=>$unverified_quantity]);

	    if($product['product_status']==2) {
		$inventory_status = 'pending';
            }
            $prod_qt=DB::table('products')->where(['id'=> $prod_id])->value('quantity');
            DB::table('products')->where(['id'=> $prod_id])->update(['quantity' =>$product['verified_quantity']]);
          
           // if($updated_qt < $inv_prod->total_quantity)
           // {
           //     $status = false;
           // }
        }
        if ($status) {
            $inventory->operator_status = 3;
            $inventory->verifier_status = 3;
            $inventory->save();
            }
         $inventory->inventory_status = $inventory_status;
	 $inventory->save();

        $response['code']=1;
        $response['msg']='';
        $response['data']= "inventory has been update!";
        return response()->json($response);
    }
    public function deleteInventory(Request $request)
    {
        $response = [];
        try {
            if (!$request->id) {
                throw  new \Exception("the arguments id needed");
            }

            DB::transaction(function () use ($request) {
                $inventory = Inventory::find($request->id);
                if ($inventory) {
                    $inventory->delete();
                    InventoryProduct::where('inventory_id', $request->id)->delete();
                }
            });
            $response['code'] = 0;
            $response['msg'] = '';
            $response['data'] = "inventory has been delete!";
        } catch (\Exception $e) {
            $response['code'] = 1;
            $response['msg'] = '';
            $response['data'] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function getInventoryProductList(Request $request)
    {
        try {
            $inventory_id = $request->get('inventory_id') * 1;
            if (!$inventory_id) {
                throw new \Exception('need inventory_id');
            }
            $stock_min = $request->get('stock_min', 0);
            $stock_max = $request->get('stock_max', 0);
            $rs = InventoryProduct::where('inventory_id', $inventory_id)->when($stock_min || $stock_max, function ($query) use ($stock_min, $stock_max) {
                $query->whereBetween('total_quantity', [$stock_min, $stock_max]);
            })->paginate();
            $response['code'] = 0;
            $response['msg'] = '';
            $response['data'] = $rs;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['code'] = 1;
            $response['msg'] = '';
            $response['data'] = $e->getMessage();
            return response()->json($response);
        }
    }

  
    


}
