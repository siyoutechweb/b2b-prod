<?php namespace App\Http\Controllers\fund;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class FundsController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function transfertPayment(Request $request){
        $supplier=AuthController::me();
        $funds = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner:id,first_name,last_name'])
                    ->where('supplier_id', $supplier->id)
                    ->whereHas('paymentmethods',function ($q){$q->where('name','Bank Transfers');})
                    ->get();
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


        public function fundById(Request $request, $fund_id)
        {
            $fund = Fund::with(['order'=>function($q){$q->with('statut:id,statut_name');},'shop_owner'])
                        ->with('paymentmethods:id,name')
                        ->find($fund_id);
            return response()->json($fund,200);
        }
    

}

