<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiyouCommission;
use App\Models\User;
use App\Models\Order;
use App\Models\Role;

class SiyouCommissionsController extends Controller {

    public function siyouCommission(Request $request){
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) 
        {
            $supplier_id=$request->input('Supplier_id');
            $commission_percent=$request->input('commission');
            $siyoucommission = SiyouCommission::where('supplier_id', '=', $supplier_id)
                               ->with('user')->exists();
            if ($siyoucommission ) 
            {
                $siyoucommission = SiyouCommission::where('supplier_id', '=', $supplier_id)
                ->with('user')->first();
                $siyoucommission->commission_percent = $commission_percent;
                $siyoucommission->save();
                return response()->json(["msg"=>"commission updated successfuly"],200);
        
            }
            $commission=new SiyouCommission();
            $commission->supplier_id=$supplier_id;
            $commission->commission_percent=$commission_percent;
            if($request->has('deposit'))
            {
                $commission->deposit=$request->input('deposit');
                $commission->Deposit_rest=$request->input('deposit');
            }
            else 
            {
                $commission->deposit=0;
                $commission->Deposit_rest=0;
            }
            if($request->has('commission_amount'))
            {
                $commission->commission_amount=$request->input('commission_amount');
            }
            else $commission->commission_amount=0;

            $commission->save();
            return response()->json(["msg"=>"commission added successfuly"],200);
            }
        return response()->json(["msg"=>"ERROR"],500);
    }
    public function addCommission(Request $request){
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $commission=new SiyouCommission();
        $commission->supplier_id=$request->input('choose Supplier');
        $commission->commission_percent=$request->input('put commission percent');
        $commission->deposit=0;
        $commission->commission_amount=0;
        $commission->Deposit_rest=0;
        $commission->order_id=0;
        $commission->save();
        return response()->json(["msg"=>"commission added successfuly"],200);
        }
        return response()->json(["msg"=>"ERROR"],500);
    }
    public function addSiyouCommission(Request $request){
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $supplier_id=$request->input('choose Supplier');
        $siyoucommission = SiyouCommission::where('supplier_id', '=', $supplier_id)
        ->with('user')
        ->get();
        if(count($siyoucommission)>0){
            return response()->json($siyoucommission);
        }
        $commission=new SiyouCommission();
        $commission->supplier_id=$supplier_id;
        $commission->commission_percent=$request->input('put commission percent');
        $commission->deposit=0;
        $commission->commission_amount=0;
        $commission->Deposit_rest=0;
        $commission->order_id=0;
        $commission->save();
        return response()->json(["msg"=>"commission added successfuly"],200);
return response()->json(["error"],500);

        }
    }
    public function updateSiyouCommission(Request $request){
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $supplier_id=$request->input('choose Supplier');
        $commission_percent=$request->input('put commission percent');
        $siyoucommission = SiyouCommission::where('supplier_id', '=', $supplier_id)
        ->with('user')
        ->where(function ($query)
        {
            $query->where('commission_percent', '!=', 0);
        })
        ->update(['commission_percent'=>$commission_percent]);
        if($siyoucommission){
        return response()->json(["msg"=>"commission percent has been updated successfuly"],200);
        }
        return response()->json(["error"],500);
    }
    }
    public function updateCommission($id, Request $request)
    {    $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $commission = SiyouCommission::findOrFail($id);
        $commission->commission_percent = $request->input('put commission percent');
        $commission->Deposit=$request->input('put the deposit amount');
        $commission->save();
            return response()->json(["msg" => "success!!"],200);
        }
        return response()->json(["msg" => "error!!"],500);
    }
    public function DeleteCommission($id)
    {
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $commission = SiyouCommission::findorfail($id);
        $commission->delete();
        return response()->json("Commission has been Deleted Successfully !");
        }
        return response()->json("ERROR !!");
    }
    public function GetCommission($id)
    {
        $siyoucommission = SiyouCommission::with('user')->with('order')->where('id', $id)->get();
        return response()->json($siyoucommission);
    }
    public function GetsupplierCommission()
    {
        $supplier=AuthController::me();
        $siyoucommission = SiyouCommission::with('user')->with('order')->where('supplier_id', $supplier->id)->get();
        return response()->json($siyoucommission);
    }
    public function GetsupplierswithCommission(Request $request)
    {     $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $suppliers=User::with('siyoucommissions','orders')
         ->where(function ($query)
        {
            $query->where('validation', '=', 1);
        })->get();
        if(count($suppliers)> 0){
        return response()->json($suppliers);
        }
        return response()->json(["msg"=>"there's no supplier validated!"],500);
    }
    }
    public function GetCommissionlist()
    {
        $siyoucommission_list = SiyouCommission::with('user')->with('order')->get();
        return response()->json($siyoucommission_list);
    }
    public function UpdateDeposit(Request $request,$supplier_id)
    {
        $user=AuthController::me();
        if ($user->hasRole('Super_Admin')) {
        $siyoucommission = SiyouCommission::findorfail($supplier_id);
        $siyoucommission->deposit=$request->input('Deposit Amount');
        $user->save();
        return response()->json(['msg' => 'Supplier Deposit has been Updated'], 200);
    }
         return response()->json(['msg'=>'ERROR!'],500);
    
}
}
