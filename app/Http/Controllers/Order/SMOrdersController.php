<?php namespace App\Http\Controllers\Order;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SMOrdersController extends Controller {

    public function getOrderList(Request $request)
    {
        $date=$request->input('date'); 
    }
    public function getSalesmanagerOrderList(Request $request)
    {
        
        return $request;
        $salesmanager = AuthController::me();
        $shopIds= $salesmanager->shopOwners()->distinct()->pluck('shop_owner_id');
        $supplierIds= $salesmanager->suppliers()->distinct()->pluck('supplier_salesmanager_shop_owner.supplier_id');
        $response['invalid_order']= $this->getValidOrder($shopIds,$supplierIds,$date);
        $response['valid_order']= $this->getInvalidOrders($shopIds,$supplierIds,$date);
        $response['paid_order']= $this->getPaidOrders($shopIds,$supplierIds,$date);

        return response()->json($response);
    }
    
    private function getValidOrder($shopIds,$supplierIds,$date)
    {
        $validList = Order::with(['productItem'=>function ($query) 
            { $query->with('product','images','CriteriaBase')->get();}])
            // ->with('commissions')
            ->with('supplier')
            ->with('shopOwner')
            ->with('paymentmethods:id,name')
            ->with('statut:id,statut_name')
            ->whereIn('supplier_id', $supplierIds)->whereIn('shop_owner_id', $shopIds)
            ->where('statut_id',4)
            ->when($date != '',  function ($q) use ($date)
            {$q->whereDate('created_at',$date);})
            ->orderBy('updated_at', 'DESC')->get();
        return $validList;
    }

    private function getInvalidOrders($shopIds,$supplierIds,$date)
    {
        $invalidList = Order::with(['productItem'=>function ($query) 
            { $query->with('product','images','CriteriaBase')->get();}])
            // ->with('commissions')
            ->with('supplier')
            ->with('shopOwner')
            ->with('paymentmethods:id,name')
            ->with('statut:id,statut_name')
            ->whereIn('supplier_id', $supplierIds)->whereIn('shop_owner_id', $shopIds)
            ->when($date != '',  function ($q) use ($date)
                {$q->where('created_at',$date);})
            ->where('statut_id',3)->orderBy('updated_at', 'DESC')->get();
        return $invalidList;
    }

    private function getPaidOrders($shopIds,$supplierIds,$date)
    {
        $paidList = Order::with(['productItem'=>function ($query) 
            { $query->with('product','images','CriteriaBase')->get();}])
            // ->with('commissions')
            ->with('supplier')
            ->with('shopOwner')
            ->with('paymentmethods:id,name')
            ->with('statut:id,statut_name')
            ->whereIn('supplier_id', $supplierIds)->whereIn('shop_owner_id', $shopIds)
            ->when($date != '',  function ($q) use ($date)
                {$q->where('created_at',$date);})
            ->where('statut_id',5)->orderBy('updated_at', 'DESC')->get();
        return $paidList;
    }

}
