<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;

class CommissionsController extends Controller
{

    public function addCommission(Request $request)
    {
        $supplier=AuthController::me();
        $type= $request->input('commission_type');
        $items = $request->input('items_id');
        $commission = new Commission();
        if($type == "by product")
            {
                $commission->shop_owner_id = $request->input('shop_owner_id');
                $commission->supplier_id = $supplier->id;
                $commission->salesmanager_id = $request->input('sales_manager_id');
                $commission->commission_percent = $request->input('commission_percent');
                $commission->commission_type = $type;
                $commission->save();
            }
        else {
            if($type == "by product")
            {
                $commission->items()->attach($items);
            }
            return response()->json(["msg" => "success!!"]);
        }
        return response()->json(["msg" => "error!!"]);
    }

    public function getShopCommissions(Request $request)
    {
        $supplier=AuthController::me();
        $commissionByShop= commission::with('shop_owner:id,first_name,last_name,email')
                        ->with('sales_manager:id,first_name,last_name,email')
                        ->where(['supplier_id'=>$supplier->id,'commission_type'=>'by shop'])
                        ->get();
        return response()->json($commissionByShop);
    }

    public function getItemsCommissions(Request $request)
    {
        $supplier=AuthController::me();
        $commissionByItems= commission::with('shop_owner:id,first_name,last_name,email')
                        ->with('sales_manager:id,first_name,last_name,email')
                        ->with(['items'=> function($q){$q->with('product');}])
                        ->where(['supplier_id'=>$supplier->id,'commission_type'=>'by product'])
                        ->get();
        return response()->json($commissionByItems);
    }


    public function updateCommission($id, Request $request)
    {
        $commission = Commission::findOrFail($id);
        $commission->commission_percent = $request->input('commission_percent');
        if ($commission->save()) {
            return response()->json(["msg" => "success!!"]);
        }
        return response()->json(["msg" => "error!!"]);
    }
    public function DeleteCommission($id)
    {
        $commission = Commission::findorfail($id);
        $commission->delete();
        return response()->json("Commission has been Deleted Successfully !");
        return response()->json("ERROR !!");
    }
    public function GetCommissionbySupplier($id)
    {

        $sales_manager_list = User::with('commissions')->where('role_id', 1)->where('supplier_id', $id)->get();
        return response()->json($sales_manager_list);
    }

//commission for sales manager

    // public function salesmanager($id)
    // {
    //     $salesmamnager=AuthController::me();
    //     $commissionByShop= commission::with('shop_owner:id,first_name,last_name,email')
    //                     ->with('supplier:id,first_name,last_name,email')
    //                     ->where(['salesmamnager_id'=>$salesmamnager->id,'commission_type'=>'by shop'])
    //                     ->get();
    //     return response()->json($commissionByShop);
    // }
}

