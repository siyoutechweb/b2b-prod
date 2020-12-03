<?php namespace App\Http\Controllers\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Wholesaler;
use App\Models\User;
use App\Models\SupplierWholesaler;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class WholeSalersController extends Controller {
      
    public function getWholesalerList(Request $request)

    {
        // $name = $request->input('name');
        // $contact = $request->input('contact');
        // //$category = $request->input('category_id');
        // //$barcode = $request->input('barcode');
        // $tax_number = $request->input('tax_number');
        // $keyWord = $request->input('keyword');
    $supplier = AuthController::me();
     //$shop_owner->shop()->value('id');
        //$supplierListe = $shop_owner->supplier;
        $wholesalers_id = SupplierWholesaler::where('supplier_id',$supplier->id)->pluck('wholesaler_id');
      //var_dump($suppliers_id) ;
        $wholesalersListe = DB::table('wholesalers')->whereIn('id',$wholesalers_id)->get();
        //var_dump($supplierListe) ;
        // if($request->has('name') && !empty($request->input('name') )) {
        //     $name = $request->input('name');
        //     $supplierListe =array_values( array_filter($supplierListe->toArray(),function($query) use($name) {
               
        //         return $query['supplier_name']==$name ||$query['company_name']==$name;
        //     }));
        // }
        // if($request->has('contact') && !empty($request->input('contact') )) {
        //     $contact = $request->input('contact');
        //     $supplierListe =array_values( array_filter($supplierListe->toArray(),function($query) use($contact) {
               
        //         return $query['contact']==$contact;
        //     }));
        // }
        // if($request->has('tax_number') && !empty($request->input('tax_number') )) {
        //     $tax_number = $request->input('tax_number');
        //     $supplierListe =array_values( array_filter($supplierListe->toArray(),function($query) use($tax_number) {
               
        //         return $query['tax_number']==$tax_number;
        //     }));
        // }
      
        $response['msg']="";
        $response['code']=1;
        $response['data']=$wholesalersListe;
        return response()->json($response);
    }
    public function getwholesalers()
    {
        $wholesalers = Wholesaler::all();
        return response()->json($wholesalers);
    }
    public function addwholesalersToSupplier(Request $request)

    {
        $supplier = AuthController::me();
        $wholesalers = $request->input('wholesalers');
        

        foreach($wholesalers as $wholesaler) {
            DB::table('supplier_wholesaler')->insert(['supplier_id'=>$supplier->id,'wholesaler_id'=>$wholesaler]);
        }
        return response()->json(['code'=>'1','msg'=>'wholesalers selected']);
    }
    public function removewholeSalerFromSupplier(Request $request,$id)
    {
        $supplier = AuthController::me();
        
        $tmp = DB::table('supplier_wholesaler')->where('wholesaler_id' ,$id)->where('supplier_id',$supplier->id)->exists();
        if(!$tmp) {
            return response()->json(['code'=>'0','msg'=>'wholesaler not found']);
        }
        DB::table('supplier_wholesaler')->where('wholesaler_id' ,$id)->where('supplier_id',$supplier->id)->delete();
     
        
        ;
        return response()->json(['code'=>'1','msg'=>'wholesaler removed successfully']);
    }  
    
    public function getWholeSalerById(Request $request,$id)
    {
        $supplier = AuthController::me();
        
        $wholesaler = Wholesaler::find($id);
        if($wholesaler) {
             $response['msg']="";
        $response['code']=1;
        $response['data']=$wholesaler;
        } else {
            $response['msg']="";
            $response['code']=0;
            $response['data']="no wholesaler found";
        }
       
        return response()->json($response);
    }
    public function addWholeSaler(Request $request)
    {
      $supplier = AuthController::me();

        
        $wholesaler = new Wholesaler();
        $password = $request->input('password');
        $wholesaler->wholesaler_name = $request->input('wholesaler_name');
        $wholesaler->company_name = $request->input('company_name');
        $wholesaler->email = $request->input('email');
        $wholesaler->description = $request->input('description');

       $wholesaler->contact =$request->input('contact');
         $wholesaler->city = $request->input('city');
       $wholesaler->tax_id=$request->input('tax_id');
        $wholesaler->website = $request->input('website');
         $wholesaler->adress = $request->input('adress');
       $wholesaler->country =$request->input('country');
         $wholesaler->province = $request->input('province');
         $wholesaler->postal_code =$request->input('postal_code');
        $wholesaler->longitude = $request->input('lng');
        $wholesaler->latitude = $request->input('lat');
      
        if ($request->hasFile('wholesaler_img')) {
            $path = $request->file('wholesaler_img')->store('wholesalers','google');
            $fileUrl = Storage::url($path);
            $wholesaler->img_url = $fileUrl;
            $wholesaler->img_name = basename($path);

        }
      
        if ($wholesaler->save()) {
            DB::table('supplier_wholesaler')->insert(['wholesaler_id'=>$wholesaler->id,
            'supplier_id'=>$supplier->id]);
     
            return response()->json(["msg" => "wholesaler added successfully !"], 200);
        }
        return response()->json(["msg" => "ERROR !"], 500);
    }

}
