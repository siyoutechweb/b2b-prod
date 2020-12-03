<?php namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\User;

class LogisticServicesController extends Controller {
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getLogisticService(Request $request)
    {
        $supplier = AuthController::me();
        // $supplier= user::find($auth->id);
        $supplier->logistic_service = true;
        if($supplier->save())
        {
            return response()->json(['msg'=>'Data saved !!'],200);
        }
        return response()->json(['msg'=>'Error !!'],500);
    }

    public function removeLogisticService(Request $request)
    {
        $supplier = AuthController::me();
        // $supplier= user::find($auth->id);
        $supplier->logistic_service = false;
        if($supplier->save())
        {
            return response()->json(['msg'=>'Data saved !!'],200);
        }
        return response()->json(['msg'=>'Error !!'],500);
    }
}
