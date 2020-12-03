<?php namespace App\Http\Controllers\Purchase;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\SupplierFund;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use mysql_xdevapi\Exception;
class FundsController extends Controller {
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function addPaymentMethod(Request $request)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
       
        if(!$request->has('name')) {
            return response()->json(['code'=>0,'msg'=>'method name is required']);
        }
        $method_name=$request->input('name');

      
        $days =$request->has('days')? $request->input('days'):0;
        DB::table('supplier_payment_methods')->insert([
        'user_id'=>$user->id,
        'name'=>$method_name,
        'days'=>$days,
        'created_at'=>Carbon::now(),
        'updated_at'=>Carbon::now()]);
  
        $response['code']=1;
        $response['msg']='payment method added successfully';
        
        return response()->json($response);
    }
    public function getPaymentMethods(Request $request)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
       
        
            $data = DB::table('supplier_payment_methods')->where('user_id',$user->id)->get(); 
        

        $response['code']=1;
        $response['msg']='success';
        $response['data'] = $data;
        return response()->json($response);
    }
    public function editPaymentMethod(Request $request,$id)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
       
        if(!$request->has('name')) {
            return response()->json(['code'=>0,'msg'=>'method name is required']);
        }
        $method_name=$request->input('name');

        
        $days =$request->has('days')? $request->input('days'):0;
        DB::table('supplier_payment_methods')
    ->where('id', $id)
    ->where('user_id',$user->id)
    ->update([
       
        'name'=>$method_name,
        'days'=>$days,
        'updated_at'=>Carbon::now()
    ]);

        $response['code']=1;
        $response['msg']='payment method successfully updated';
        
        return response()->json($response);
    }
    public function deletePaymentMethod(Request $request,$id)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        DB::table('supplier_payment_methods')->where('id', $id)->where('user_id',$user->id)->delete();

        $response['code']=1;
        $response['msg']='payment method successfully deleted';
        
        return response()->json($response);
    }
    public function getPaymentMethodById(Request $request,$id)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        $data = DB::table('supplier_payment_methods')->where('id', $id)->where('user_id',$user->id)->first();

        $response['code']=1;
        
        $response['data'] = $data;
        
        return response()->json($response);
    }
    public function addFund(Request $request)
    {
        $user= AuthController::me();
       // echo $user;
        if(!($user->role_id==2)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        
        if(!$request->has('amount')) {
            return response()->json(['code'=>0,'msg'=>'payment amount is required']);
        }
        $payment_method_id = $request->input('payment_method_id');
	$wholesaler_id = $request->input('wholesaler_id');
        
        //echo $days[0]->days;
        if($days =DB::table('supplier_payment_methods')->where('id',$payment_method_id)->get('days')->toArray()) {
            $days =  $days[0]->days;

        }else {
	return response()->json(['code'=>0,'msg'=>'payment method is required']);

}
        //echo gettype($days);
        $amount=$request->input('amount');

        
        
        $start_date = $request->input('start_date');
        if($days > 0) {
                    $finish_date = strtotime("+".$days." day", strtotime($start_date));
                    $finish_date = date("Y-m-d H:i:s",$finish_date);

        }else {
            $finish_date = $start_date;
        }
        //echo $finish_date;
        $status='not paid';
        if ($request->hasFile('fund_image')) {
            $path = $request->file('fund_image')->store('funds', 'google');
            $fileUrl = Storage::url($path);
            $img_url = $fileUrl;
            $img_name = basename($path);

        }else {
            $img_url =null;
            $img_name=null;
        }
        DB::table('supplier_funds')->insert(['amount'=>$amount,
        
        'user_id'=>$user->id,
        'start_date'=>$start_date,
        'finish_date'=>$finish_date,
        'payment_method_id'=>$payment_method_id,
	'wholesaler_id'=>$wholesaler_id,
        'status'=>$status,
        'img_url'=>$img_url,
        'created_at'=>Carbon::now(),
        'updated_at'=>Carbon::now(),
        'img_name'=>$img_name]);
  
        $response['code']=1;
        $response['msg']='fund added successfully';
        
        return response()->json($response);
    }

    public function getFundById(Request $request,$id)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        $data = DB::table('supplier_funds')->where('id', $id)->where('user_id',$user->id)->first();

        $response['code']=1;
        
        $response['data'] = $data;
        
        return response()->json($response);
    }
    public function getFunds1(Request $request) {
        $user= AuthController::me();
       // echo $user;
        if(!($user->role_id==2)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
     
        
        
        $created_at = $request->input('created_at');
        $payment_date = $request->input('payment_date');
        $status = $request->input('status');
        $payment_method = $request->input('payment_method');
      	$wholesaler_id = $request->input('wholesaler_id');

       
        if(!$created_at) {
            $created_at = '';
        }
        if(!$status) {
            $status = '';
        }
      
        if(!$payment_method) {
            $payment_method = '';
        }
        if(!$payment_date) {
            $payment_date = '';
        }
      
        $response= SupplierFund::with('paymentmethods')->with('wholesaler')->where('user_id',$user->id)
        
        ->when($payment_method != '', function ($query) use ($payment_method) {
            $query->where('payment_method_id',$payment_method);})
        
        ->when($status != '', function ($query) use ($status) {
            $query->where('status',$status);})
        ->when($created_at != '', function ($query) use ($created_at) {
            $query->whereDate('created_at','=',$created_at);})
        ->when($payment_date != '', function ($query) use ($payment_date) {
            $query->whereDate('finish_date','<=',$payment_date);})
            ->when($wholesaler_id!= '', function ($query) use ($wholesaler_id) {
            $query->where('wholesaler_id',$wholesaler_id);})

            ->orderBy('id','desc')->paginate(20);//->findOrFail();
            $response ->code = '1';
            $response ->msg = "success";
            return response()->json($response);
    }

    public function updateFund(Request $request,$id)
    {
        $user= AuthController::me();
       // echo $user;
        if(!($user->role_id==2)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        
        if(!$request->has('amount')) {
            return response()->json(['code'=>0,'msg'=>'payment amount is required']);
        }
        $payment_method_id = $request->input('payment_method_id');
        $days =DB::table('supplier_payment_methods')->where('id',$payment_method_id)->get('days')->toArray();
        //echo $days[0]->days;
        if($days) {
            $days =  $days[0]->days;
        }
        //echo gettype($days);
        $amount=$request->input('amount');

      
        $start_date = $request->input('start_date');
        if($days > 0) {
                    $finish_date = strtotime("+".$days." day", strtotime($start_date));
                    $finish_date = date("Y-m-d H:i:s",$finish_date);

        }else {
            $finish_date = $start_date;
        }
        //echo $finish_date;
        $status=$request->input('status');
        if ($request->hasFile('fund_image')) {
            $path = $request->file('fund_image')->store('funds', 'google');
            $fileUrl = Storage::url($path);
            $img_url = $fileUrl;
            $img_name = basename($path);

        }else {
           $img_url =null;
            $img_name = null;

        }
	$wholesaler_id = $request->input('wholesaler_id');

        DB::table('supplier_funds')
        ->where('id', $id)
        ->where('user_id',$user->id)
        ->update([
        'amount'=>$amount,
        'wholesaler_id'=>$wholesaler_id,
        'start_date'=>$start_date,
        'finish_date'=>$finish_date,
        'payment_method_id'=>$payment_method_id,
        'status'=>$status,
        'img_url'=>$img_url,
        
        'updated_at'=>Carbon::now(),
        'img_name'=>$img_name]);
        
  
        $response['code']=1;
        $response['msg']='fund updated successfully';
        
        return response()->json($response);
    }

    public function deleteFund(Request $request,$id)
    {
        $user= AuthController::me();
        //echo $user;
        if(!($user->role_id==2 || $user->role_id==5)) {
            return response()->json(['code'=>0,'msg'=>'unsufficient permissions']);
        }
        DB::table('supplier_funds')->where('id', $id)->where('user_id',$user->id)->delete();

        $response['code']=1;
        $response['msg']='fund successfully deleted';
        
        return response()->json($response);
    }
}
