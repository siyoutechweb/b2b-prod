<?php namespace App\Http\Controllers\Order;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use App\Models\Statut;
use App\Models\SiyouCommission;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class SupplierOrdersController extends Controller {

    public function getSupplierOrderList(Request $request)
    {
        $supplier = AuthController::me();
        $date=$request->input('date');
        $orderList = Order::where('supplier_id', $supplier->id)
            ->with('shopOwner:id,first_name,last_name,adress,country,region','statut:id,statut_name')
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

    public function getSupplierInvalid()
    {
        $supplier = AuthController::me();
        $invalidOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 3])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name')
            // ->with('commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($invalidOrder, 200);
    }

    public function getSupplierValidOrder()
    {
        $supplier = AuthController::me();
        $validOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 4])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name')
            // ->with('commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($validOrder, 200);
    }

    public function getSupplierPaidOrder()
    {
        $supplier = AuthController::me();
        $paidOrder = Order::where(['supplier_id'=> $supplier->id,'statut_id'=> 5])
            ->with('shopOwner:id,first_name,last_name','statut:id,statut_name')
            // ->with('commissions:commission_amount')
            ->with(['productItem'=>function ($query) 
            { $query->with('product','images')->get();}])
            ->get();
        
        return response()->json($paidOrder, 200);
    }


    public function updateOrderStatus(Request $request, $order_id)
    {
        $supplier = AuthController::me();
        $status = $request->input('status');
        $statut = null;
        $order = Order::find($order_id);
        switch ($status) {
            case 'confirm':
                $statut = Statut::where('statut_name', 'validated by supplier')->first();
                order::addOrderToS2C($order ,$supplier);
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
  


}
