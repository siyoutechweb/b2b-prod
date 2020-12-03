<?php namespace App\Http\Controllers\User;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Supplier_Salesmanager_ShopOwner;
use App\Models\User;
use App\Models\SiyouCommission;
use App\Models\position_tracking;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SalesmanagersController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function getSupplierList()
    {
        $sales_manager = AuthController::me();
        $suppliers = $sales_manager->suppliers;
        return response()->json(["suppliers" => $suppliers]);
    }

    public function getShopList()
    {
        $sales_manager = AuthController::me();
        $suppliers = $sales_manager->shopOwners;
        return response()->json(["shops" => $suppliers]);
    }

    public function lastPosition(Request $request)
    {
        $sales_manager = AuthController::me();
        if ($request->filled (['latitude','longitude'])) {
            
            $position = new position_tracking();
            $position->latitude = $request->input('latitude');
            $position->longitude = $request->input('longitude');
            $position->user_id = $sales_manager->id;
            if ($position->save()){
                 return response()->json('success!!',200);
            }
        }
        return response()->json(["msg"=>'no data!!']);
    }

    public function positionHistory(Request $request)
    {
        
        $supplier = AuthController::me();
            
        $positionHistory = $supplier->salesmanagerToSupplier()
                            ->with(['position'=>function ($q){$q->orderBy('id','desc');}])
                            ->distinct()->get();
        return response()->json($positionHistory);
    }

}
