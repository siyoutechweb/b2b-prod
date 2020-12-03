<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use App\Models\Statut;
use App\Models\Tarif;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\Fund;
use App\Models\SiyouCommission;
use App\Models\Supplier_Salesmanager_ShopOwner;
use Carbon\Carbon;
use DateTime;
use Exception;
use App\Http\Controllers\Order\PurchasedItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{


    public function addOrder(Request $request)
    {
        $shop_owner = AuthController::me();
        $order = new Order();
        $supplier_id = $request->input('supplier_id');
        $shop_owner_id = $shop_owner->id;
        $order->shop_owner_id = $shop_owner_id;
        $statut = Statut::where('statut_name', 'waiting for supplier validation')->first();
        $order->order_price = $request->input('order_total_price');
        $order->order_weight = $request->input('order_weight');
        // $logistic_service= user::where('id',$supplier_id)->value('logistique_servie');
        // if ($logistic_service) {
        //     $order->logistic_company_id = $request->input('logistic_company_id');
        //     $order->logistic_tarif = $request->input('logistic_tarif');
        // }
        // $order->logistic_company_id = $request->input('logistic_company_id');
        // $order->logistic_tarif = $request->input('logistic_tarif');
        // $company_id = $request->input('company_id');
        // $order->company_id = $company_id;
        // $tarif = Tarif::where('company_id', $order->company_id)
        //     ->where('kg', $order->order_weight)->value('price');
        // $order->shipping_price = $tarif;
        $orderProductsList = $request->input('order_products_list');
        $payment_method_id = $request->input('payment_method_id');
        $order_date = Carbon::now();
        $order->payment_method_id = $payment_method_id;
        $order->order_date = $order_date;
        $order->supplier_id = $supplier_id;
        $order->statut_id = $statut->id;
        try {
            // if ($this->ifSalesmanager($supplier_id, $shop_owner_id)) {
            //     $commission = new Commission();
            //     $commission->save();
            //     if ($order->commissions()->associate($commission)->save()) {
            //         foreach ($orderProductsList as $item) {
            //             $order->productItem()->attach($item['item_id'], ['quantity' => $item['item_quantity']]);
            //         }
            //     }
            // } else 
            {
                if ($order->save()) {
                    $this->orderReference($order->id, $shop_owner);
                    foreach ($orderProductsList as $item) {
                        $order->productItem()->attach($item['item_id'], ['quantity' => $item['item_quantity']]);
                    }
                    if (filled($payment_method_id)) {
                        $fund = new Fund();
                        $fund->supplier_id = $supplier_id;
                        $fund->shop_owner_id = $shop_owner->id;
                        $fund->order_id = $order->order->id;
                        $fund->payment_date = $request->input('required_date');
                        $fund->amount = $order->order_price;
                        $fund->payment_method_id = $order->payment_method_id;
                        $fund->save();
                    }
                }
            }
            return response()->json(["msg" => "Order has been added Successfully !"], 200);
        } catch (Exception $error) {
            return response()->json(['msg' => $error], 500);
        }
    }

    private function orderReference($order_id, $shop_owner){
        $order = order::find($order_id);
        $order->order_ref= strtoupper($shop_owner->last_name[0].$shop_owner->first_name[0]).'-'.str_replace("-","",$order->order_date).'-'.$order_id;
        $order->save();
        return $order;
    }

    public function ifSalesmanager($supplier_id, $shop_owner_id)
    {
        $salesmanager = DB::table('supplier_salesmanager_shop_owner')
            ->where([
                ['shop_owner_id', '=', $shop_owner_id],
                ['supplier_id', '=', $supplier_id]
            ])
            ->count();
        if ($salesmanager > 0) {
            return true;
        } else {
            return false;
        }
    }


    // public function ifSalesmanager(Request $request)
    // {
    //     $order = new Order();
    //     $supplier = AuthController::me();
    //     $shop_owner_id = $request->input('shop_owner_id');
    //     $salesmanager = DB::table('supplier_salesmanager_shop_owner')
    //         ->where([
    //             ['shop_owner_id', '=', $shop_owner_id],
    //             ['supplier_id', '=', $supplier->id]
    //         ])
    //         ->count();
    //         // echo $salesmanager;
    //     if ($salesmanager > 0) {
    //         $commission = new Commission();
    //         $order->order_price = 12;
    //         $order->supplier_id = $supplier->id;
    //         $order->shop_owner_id = $shop_owner_id;
    //         $order->statut_id = 1;
    //         $commission->save();
    //         // $order->save();
    //         $order->commissions()->associate($commission)->save();
    //     }
    // }



    public function getSupplierInvalid()
    {
        $supplier = AuthController::me();
        $invalidOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 3])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name','commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($invalidOrder, 200);
    }

    public function getSupplierValidOrder()
    {
        $supplier = AuthController::me();
        $validOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 4])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name','commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($validOrder, 200);
    }

    public function getSupplierPaidOrder()
    {
        $supplier = AuthController::me();
        $paidOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 5])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name','commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($paidOrder, 200);
    }

    public function getOrderById(Request $request, $order_id)
    {
        $supplier = AuthController::me();
        $order = Order::where('id',$order_id)
            ->with('shopOwner','statut:id,statut_name','supplier')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images','CriteriaBase')->get();}])
            ->with('paymentmethods:id,name')
            ->first();
        
        return response()->json($order, 200);
    }

    public function getSalesmanagerCommissionAmount($supplier_id, $shop_owner_id)
    {
        $result = DB::table('supplier_salesmanager_shop_owner')
            ->select('commission_amount')
            ->where([
                ['shop_owner_id', '=', $shop_owner_id],
                ['supplier_id', '=', $supplier_id]
            ])
            ->first();
        return $result->commission_amount;
    }

    public function getSalesmanagerOrderList()
    {
        $salesmanager = AuthController::me();
        $supplierIds = DB::table('supplier_salesmanager_shop_owner')
            ->where('salesmanager_id', $salesmanager->id)
            ->whereNotNull('shop_owner_id')
            ->pluck('supplier_id');
        $shopIds = DB::table('supplier_salesmanager_shop_owner')
            ->where('salesmanager_id', $salesmanager->id)
            ->whereNotNull('shop_owner_id')
            ->pluck('shop_owner_id');
            
        $orderList = Order::with(['productItem'=>function ($query) 
            { $query->with('product','images','CriteriaBase')->get();}])
            // ->with('commissions')
            ->with('supplier')
            ->with('shopOwner')
            ->with('paymentmethods:id,name')
            ->with('statut:id,statut_name')
            ->whereIn('supplier_id', $supplierIds)->whereIn('shop_owner_id', $shopIds)->get();
        return response()->json($orderList, 200);
    }

    public function updateOrderStatus(Request $request, $order_id)
    {
        $status = $request->input('status');
        $statut = null;
        $order = Order::find($order_id);
        switch ($status) {
            case 'confirm':
                // $this->calculateCommissionValue($order);
                // $this->calculatesiyouCommissionValue($order);
                $statut = Statut::where('statut_name', 'validated by supplier')->first();
                break;
            case 'rejected':
                $statut = Statut::where('statut_name', 'rejected by supplier')->first();
                break;
        }
        $order->statut_id = $statut['id'];
        if ($order->save()) {
            return response()->json(["msg" => "Order Updated Succeffully !"], 200);
        }
        return response()->json(["msg" => "Error while updating Order !"], 500);
    }

    public function calculateCommissionValue($order)
    {
        $user = AuthController::me();
        $result = DB::table('supplier_salesmanager_shop_owner')
            ->select('commission_amount')
            ->where([
                ['supplier_id', '=', $user->id],
                ['shop_owner_id', '=', $order->shop_owner_id]
            ])->first();
        $result1 = DB::table('siyoucommission')
            ->select('commission_percent')
            ->where(
                'supplier_id',
                '=',
                $order->supplier_id
            )->first();
        $commission_amount = $result->commission_amount;
        $siyoucommission_percent = $result1->commission_percent;
        $commission_value = ($order->order_price * $commission_amount) / 100;
        $siyoucommission_value = ($order->order_price * $siyoucommission_percent) / 100;
        $order = Order::where('id', $order->id)
            ->update(['commission' => $commission_value]);
        $siyoucommission = SiyouCommission::where('supplier_id', $user->id)->first();
        $newsiyoucommission = $siyoucommission->replicate();
        $newsiyoucommission->commission_amount = $siyoucommission_value;
        $newsiyoucommission->Deposit_rest = $newsiyoucommission->Deposit - $siyoucommission_value;
        $newsiyoucommission->order_id = $order->id;
        $newsiyoucommission->save();
    }
    /**
     * statics
     */

    public function GetOrderByDate(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier', 'Shop_Owner')) {
            $order_date = $request->input('order_date');
            $order = Order::where('order_date', $order_date)
                ->with('supplier')
                ->with('shopOwner')
                ->with('statut')
                ->with('paymentmethods')
                ->with('company')->get();
            return response()->json($order);
        }
        return response()->json("ERROR");
    }
    public function GetOrderBypaymentDate(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier', 'Shop_Owner')) {
            $ldate = new DateTime('today');
            $order = Order::where('required_date', $ldate)
                ->with('supplier')
                ->with('shopOwner')
                ->with('statut')
                ->with('paymentmethods')
                ->with('company')->get();
            return response()->json($order);
        }
        return response()->json("ERROR");
    }
    public function GetOrderByOrderPrice(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier', 'Shop_Owner')) {
            $max_price = $request->input('choose the max price');
            $min_price = $request->input('choose the min price');
            $order = Order::where('order_price', '>=', $min_price)->where('order_price', '<=', $max_price)
                ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
                ->with('supplier')
                ->with('shopOwner')
                ->with('statut')
                ->with('paymentmethods')
                ->with('company')->get();
            return response()->json($order);
        }
        return response()->json("ERROR");
    }
    public function GetOrderByCommission(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier', 'sales_manager')) {
            $max_commission = $request->input('choose the max commission');
            $min_commission = $request->input('choose the min commission');
            $order = Order::where('commission', '>=', $min_commission)->where('commission', '<=', $max_commission)
                ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
                ->with('supplier')
                ->with('shopOwner')
                ->with('statut')
                ->with('paymentmethods')
                ->with('company')->get();
            return response()->json($order);
        }
        return response()->json("ERROR");
    }
    public function GetOrderByCompany()
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier', 'Shop_Owner')) {
            $company = Company::where('name', Input::get('choose the company'))->pluck('id');
            $order = Order::where('company_id',  $company)
                ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
                ->with('supplier')
                ->with('shopOwner')
                ->with('statut')
                ->with('paymentmethods')
                ->with('company')->get();
            return response()->json($order);
        }
        return response()->json("ERROR");
    }
    public function GetOrderByWeigh(Request $request)
    {
        $user = AuthController::me();
        $order = Order::where('order_weight', '=',  Input::get('put the order Weigh'))
            ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
            ->with('supplier')
            ->with('shopOwner')
            ->with('statut')
            ->with('paymentmethods')
            ->with('company')->get();
        return response()->json($order);
        return response()->json("ERROR");
    }
    public function GetOrderByPaymentMethod(Request $request)
    {
        $user = AuthController::me();
        $payment = PaymentMethod::where('name', Input::get('choose the Payment Method'))->pluck('id');
        $order = Order::where('payment_method_id',  $payment)
            ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
            ->with('supplier')
            ->with('shopOwner')
            ->with('statut')
            ->with('paymentmethods')
            ->with('company')->get();
        return response()->json($order);
        return response()->json("ERROR");
    }
    public function GetOrderBydaterange(Request $request)
    {
        $user = AuthController::me();
        $order = Order::where('order_date', '>=',  Input::get('put the first date'))->where('order_date', '<=', Input::get('put the second date'))
            ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
            ->with('supplier')
            ->with('shopOwner')
            ->with('statut')
            ->with('paymentmethods')
            ->with('company')->get();
        return response()->json($order);
        return response()->json("ERROR");
    }
    public function GetOrderByStatus(Request $request)
    {
        $user = aUTHcontroller::me();
        $status = Statut::where('statut_name', Input::get('choose the status'))->pluck('id');
        $order = Order::where('statut_id',  $status)
            ->where('supplier_id', '=', $user->id)->orwhere('shop_owner_id', '=', $user->id)
            ->with('supplier')
            ->with('shopOwner')
            ->with('statut')
            ->with('paymentmethods')
            ->with('company')->get();
        return response()->json($order);
        return response()->json("ERROR");
    }
    public function getbestsellingproducts()
    {
        $user = AuthController::me();
        $product = Product::select(
            'products.*',
            DB::raw('SUM(product_order.quantity) as quantity')
        )
            ->join('product_order', 'products.id', 'product_order.product_id')
            ->groupBy('product_id')->ORDERBY('quantity', 'DESC')->LIMIT(10)
            ->with('supplier')
            ->with('category')
            ->get();
        return response()->json($product);
        return response()->json("ERROR!!");
    }
}
