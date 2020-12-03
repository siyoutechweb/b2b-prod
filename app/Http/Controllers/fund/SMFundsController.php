<?php namespace App\Http\Controllers\Fund;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\Order;
use Illuminate\Support\Facades\DB;


class SMFundsController extends Controller {

    
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function transfertPayment(Request $request){
        $salesmanager=AuthController::me();
        $shopIds= $salesmanager->shopOwners()->distinct()->pluck('shop_owner_id');
        $supplierIds= $salesmanager->suppliers()->distinct()->pluck('supplier_salesmanager_shop_owner.supplier_id');
        $orders = order::with(['statut:id,statut_name','shopOwner:id,first_name,last_name','supplier:id,first_name,last_name'])
                        ->whereIn('supplier_id',$supplierIds)
                        ->whereIn('shop_owner_id',$shopIds)->get();
        return $orders;
        // $funds = Fund::with(['order'=>function($q){$q->)
        //             ->where('supplier_id', $supplier->id)
        //             ->whereHas('paymentmethods',function ($q){$q->where('name','Bank Transfers');})
        //             ->get();
            return response()->json(["transfertPayment"=>$funds]);
        }

    public function checkPayment(Request $request){
        $supplier=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
                    ->where('supplier_id', $supplier->id)
                    ->whereHas('paymentmethods',function ($q){$q->where('name','Bank check');})
                    ->get();
            return response()->json(["checkPayment"=>$funds]);
        }
    public function deliveryPayment(Request $request){
        $supplier=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
                    ->where('supplier_id', $supplier->id)
                    ->whereHas('paymentmethods',function ($q){$q->where('name','payment on delivery');})
                    ->get();
            return response()->json(["deliveryPayment"=>$funds]);
        }

    public function paypalPayment(Request $request){
        $supplier=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
                    ->where('supplier_id', $supplier->id)
                    ->whereHas('paymentmethods',function ($q){$q->where('name','Paypal');})
                    ->get();
            return response()->json(["paypalPayment"=>$funds]);
        }

    public function creditCardPayment(Request $request){
        $supplier=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
                    ->where('supplier_id', $supplier->id)
                    ->whereHas('paymentmethods',function ($q){$q->where('name','Credit Card');})
                    ->get();
            return response()->json(["CardPayment"=>$funds]);
        }
    
    public function FundsList(){
        $salesmanager=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
        ->whereIn('supplier_id', $supplier->id)
        ->with('paymentmethods')
        ->get();
            return response()->json($funds);
        }
    
 
    
        private function commission($sm_id)
        {
            $commission = DB::table('commissions')
                ->where('salesmanager_id', $sm_id)
                ->get();
            
            return $commission;
        }

}
