<?php namespace App\Http\Controllers\Order;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Statut;
use App\Models\Tarif;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\Fund;
use App\Models\SiyouCommission;
use App\Models\Commission;
use App\Models\Supplier_Salesmanager_ShopOwner;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopOrdersController extends Controller {

    public function addOrder(Request $request)
    {
        $shop_owner = AuthController::me();
        // $suppliers_id = $shop_owner->supplier()->pluck('users.id')->toArray();
        // $suppliers=implode(",",$suppliers_id);
        // $validator = Validator::make($request->all(), 
        // [ 'supplier_id' => 'required|in:'.$suppliers,
        //   'order_products_list' => 'required',
        //   'order_products_list.*' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors());
        // }
        $order = new Order();
        $supplier_id = $request->input('supplier_id');
        $shop_owner_id = $shop_owner->id;
        $order->shop_owner_id = $shop_owner_id;
        $statut = Statut::where('statut_name', 'waiting for supplier validation')->first();
        $order->order_price = $request->input('order_total_price');
        $order->order_weight = $request->input('order_weight');
        // logistic traitement
        $logistic_service= user::where('id',$supplier_id)->value('logistic_service');
        if ($logistic_service) {
            $order->logistic_company_id = $request->input('logistic_company_id');
            $tarif = Tarif::where('company_id', $order->logistic_company_id)
                            ->where('kg','>=', $order->order_weight)->value('price');
            $order->logistic_tarif = $tarif;
            // $order->shipping_price = $tarif;
        }
        $orderProductsList = $request->input('order_products_list');
        $payment_method_id = $request->input('payment_method_id');
        $order_date = Carbon::today()->toDateString();
        $order->payment_method_id = $payment_method_id;
        $order->order_date = $order_date;
        $order->supplier_id = $supplier_id;
        $order->statut_id = $statut->id;
        $this->SalesmanagerCommission($order ,$orderProductsList);
        try {
            if ($order->save()) {
                $this->orderReference($order, $shop_owner);
                $order->save();
                foreach ($orderProductsList as $item) {
                    $order->productItem()->attach($item['item_id'], ['quantity' => $item['item_quantity']]);
                }
                if (filled($payment_method_id)) {
                    $fund = new Fund();
                    $fund->supplier_id = $supplier_id;
                    $fund->shop_owner_id = $shop_owner_id;
                    $fund->order_id = $order->id;
                    $fund->payment_date = $request->input('required_date');
                    $fund->amount = $order->order_price;
                    $fund->payment_method_id = $order->payment_method_id;
                    $fund->save();
                }
            }
            return response()->json(["msg" => "Order has been added Successfully !"], 200);
        } catch (Exception $error) {
            return response()->json(['msg' => $error], 500);
        }
    }

    private function SalesmanagerCommission($order , $order_item)
    {
        $order_items = collect($order_item);
        $commission = commission::where([
                ['shop_owner_id', '=', $order->shop_owner_id],
                ['supplier_id', '=', $order->supplier_id]
            ])->whereNotNull('salesmanager_id')->first();
        if(!empty($commission)){
            $commission_percent = $commission->commission_percent;
            if ($commission->commission_type == "by shop") 
            {
                $order->commission = ($order->order_price*$commission_percent)/100;
            }
            else 
            {
                $SmCommission=0;
                $commissionitems= $commission->items()->get(['product_items.id','item_offline_price']);
                $commissionItemsId= $commissionitems->pluck('id');
                $orderItemsId= $order_items->pluck('item_id');
                $itemsId= $commissionItemsId->diff($orderItemsId)->toArray();
                $items=$commissionitems->whereIn('id',$itemsId);
                foreach ($items as $item) {
                    $SmCommission += ($item->item_offline_price*$commission_percent)/100;
                }
                $order->commission = $SmCommission;
               
            }      
            }  
    }

    private function  orderReference($order, $shop_owner)
    {
        $order->order_ref= strtoupper($shop_owner->last_name[0].$shop_owner->first_name[0]).'-'.str_replace("-","",$order->order_date).'-'.$order->id;
       
    }

    public function getShopOwnerOrderList(Request $request)
    {
        
        $shop_owner = AuthController::me();
        $date=$request->input('date');
        $orderList = Order::where('shop_owner_id', $shop_owner->id)
            ->with('supplier:id,first_name,last_name','statut:id,statut_name')
            ->with(['productItem' => function ($query) 
                { $query->select('product_items.id','item_online_price','item_barcode','product_base_id')
                        ->with('product:product_name,id')->get();}])
            ->when($date != '',  function ($q) use ($date)
                {$q->whereDate('created_at',$date);})
            ->orderBy('id','DESC')->get();
        $orderData['invalid']=$orderList->filter(function ($order){
            return  $order->statut_id == 3 ;})->values(); 
        $orderData['valid']=$orderList->filter(function ($order){
            return  $order->statut_id == 4 ;})->values();
        $orderData['paid']=$orderList->filter(function ($order){
            return  $order->statut_id == 5 ;})->values();

        return response()->json($orderData, 200);

    }

    public function getInvalidOrder(Request $request){

        $shop_owner = AuthController::me();
        $date= $request->input('date');
        $supplier_id =$request->input('supplier_id');
        $invalidOrder = Order::with('supplier:id,first_name,last_name','statut:id,statut_name')
                        ->with(['productItem' => function ($query) 
                        { $query->with('product','images')->get();}])
                        ->where(['shop_owner_id'=>$shop_owner->id,'statut_id'=>3])
                        ->when($date != '', function ($query) use ($date) {
                            $query->whereDate('created_at',$date);})
                        ->when($supplier_id != '', function ($query) use ($supplier_id) {
                                $query->where('supplier_id',$supplier_id);})
                        ->orderBy('id','DESC')->get();
        return response()->json($invalidOrder, 200);
    }

    public function getValidOrder(Request $request){
        
        $shop_owner = AuthController::me();
        $date= $request->input('date');
        $supplier_id =$request->input('supplier_id');
        $validOrder = Order::with('supplier:id,first_name,last_name','statut:id,statut_name')
                        ->with(['productItem' => function ($query) 
                        { $query->with('product','images')->get();}])
                        ->where(['shop_owner_id'=>$shop_owner->id,'statut_id'=>4])
                        ->when($date != '', function ($query) use ($date) {
                            $query->whereDate('created_at',$date);})
                        ->when($supplier_id != '', function ($query) use ($supplier_id) {
                                $query->where('supplier_id',$supplier_id);})
                        ->orderBy('id','DESC')->get();
        return response()->json($validOrder, 200);
    }

    public function getPaidOrder(Request $request){
        
        $shop_owner = AuthController::me();
        $date= $request->input('date');
        $supplier_id =$request->input('supplier_id');
        $paidOrder = Order::with('supplier:id,first_name,last_name','statut:id,statut_name')
                        ->with(['productItem' => function ($query) 
                        { $query->with('product','images')->get();}])
                        ->where(['shop_owner_id'=>$shop_owner->id,'statut_id'=>5])
                        ->when($date != '', function ($query) use ($date) {
                            $query->whereDate('created_at',$date);})
                        ->when($supplier_id != '', function ($query) use ($supplier_id) {
                                $query->where('supplier_id',$supplier_id);})
                        ->orderBy('id','DESC')->get();
        return response()->json($paidOrder, 200);
    }


}
