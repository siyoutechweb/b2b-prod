<?php namespace App\Http\Controllers\Discount;

class DiscountsController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // API which allows the admin to add a new discount type
    public function addType(Request $request) 
    {
        $newType = new Discount();
        $newType->type = $request->input('discount');
        if ($newType->save()) {
            return response()->json(["msg"=>"discount type added seccessefully"]);}
        return response()->json(["msg"=>"Error while saving"]);  
    }

    public function getType(Request $request)
    {
        $type_id= $request->input('discount_id');
        $type = Discount::find($type_id);
        $response = array();
        $response['code']=1;
        $response['msg']="";
        $response['data']= $type;
        return response()->json($response);

    } 

    public function getDiscountList(Request $request)
    {
        $type_id= $request->input('discount_id');
        $types = Discount::all();
        return response()->json($types);

    } 
    
    // API which allows the admin to update an existing discount type
    public function updateType(Request $request)
    {
        $type_id= $request->input('discount_id');
        $discount= $request->input('type');
        $type = Discount::find($type_id);
        $type->type= $discount;
        if($type->save())
        {
            $response = array();
            $response['code']=1;
            $response['msg']="";
            $response['data']='Discount type has been updated';
            return response()->json($response);
        }
        $response = array();
        $response['code']=0;
        $response['msg']="1";
        $response['data']='Error';
        return response()->json($response);   
    }
    
    // API which allows the admin to delete a discount type from database
    public function deleteType(Request $request)
    {
        $type_id= $request->input('discount_id');
        $type = Discount::find($type_id);
        if($type->delete())
        {
            $response = array();
            $response['code']=1;
            $response['msg']="";
            $response['data']='Discount type has been removed';
            return response()->json($response);
        }
        $response = array();
        $response['code']=0;
        $response['msg']="1";
        $response['data']='Error';
        return response()->json($response);   
    }

}
