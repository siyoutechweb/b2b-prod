<?php namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\CriteriaBase;
use App\Models\ProductBase;
use App\Models\ProductImage;
use App\Models\ProductItem;
use App\Models\item_criteria;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopsController extends Controller {


    public function Dashboard(Request $request)
    {
        $shop_owner = AuthController::me();
        $total_order = order::where('shop_owner_id',$shop_owner->id )->count();
        $invalid_order = order::where(['shop_owner_id'=>$shop_owner->id , 'statut_id'=>3])->count();
        $valid_order = order::where(['shop_owner_id'=>$shop_owner->id , 'statut_id'=>4])->count();
        $paid_order = order::where(['shop_owner_id'=>$shop_owner->id , 'statut_id'=>5])->count();
        $total_purchased_item =ProductItem::whereHas('orders',function ($q) use ($shop_owner){
            $q->where('shop_owner_id',$shop_owner->id);
        })->count();
        $invalid_percent =($invalid_order / $total_order) *100 ;
        $valid_percent = ($valid_order / $total_order) *100;
        $paid_percent = ($paid_order / $total_order)* 100;
        $response['total_order'] = $total_order;

        $response['invalid_order'] = $invalid_order;
        $response['valid_order'] = $valid_order;
        $response['paid_order'] = $paid_order;

        $response['inv_order_percent'] = number_format($invalid_percent, 2);
        $response['valid_order_percent'] =number_format( $valid_percent,2);
        $response['paid_order_percent'] = number_format($paid_percent,2);
        
        return response()->json($response);
    }


}
