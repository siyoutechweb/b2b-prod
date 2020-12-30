<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Supplier_Salesmanager_ShopOwner;
use App\Models\User;
use App\Models\SiyouCommission;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
// use Tymon\JWTAuth\JWTAuth;

class UsersController extends Controller
{

    // const MODEL = "App\Users";

    // use RESTActions;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['addSupplier', 'addShop_Owner', 'addSalesManager','signUp','signUpShop','createUsers','getRoles','updateShopOwner']]);
    }
    public function account()
    {
        $user = AuthController::me();
        $user = user::where('id', $user->id)->with('role')->get();
        return response()->json($user);
    }
      public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }



    public function getInvalidUsers(Request $request)
    {
        $user = AuthController::me();
	//echo $user;
        if ($user->hasRole('Super_Admin')) {
            $invalidList = user::where('validation', 0)->with('role')->get();
            return response()->json($invalidList, 200);
        }
        return response()->json(['msg' => 'ERROR'], 500);
    }
    public function getvalidUsers(Request $request)
    {
        $user = AuthController::me();

        if ($user->hasRole('Super_Admin')) {
            $validList = user::where('validation', 1)->with('role')->get();
            return response()->json($validList, 200);
        }
        return response()->json(['msg' => 'ERROR'], 500);
    }
    public function getInvalidUsersLast(Request $request)
    {
        $user = AuthController::me();
//echo $user;
        if ($user->hasRole('Super_Admin')) {
            $invalidList = user::where('validation', 0)->with('role')->orderBy('id', 'DESC')->take(10)->get();
            return response()->json($invalidList, 200);
        }
        return response()->json(['msg' => 'ERROR'], 500);
    }
    public function validateUser($user_id)
    {
        $superadmin = AuthController::me();
	//echo $superadmin->role_id;
        if ($superadmin->hasRole('Super_Admin')) {
            $user = user::findorfail($user_id);
            $user->validation = 1;
            $user->save();
            return response()->json(['msg' => 'user account has been validated'], 200);
        }
        return response()->json(['msg' => 'ERROR!'], 500);
    }
    public function blockUser($user_id)
    {
        $superadmin = AuthController::me();
        if ($superadmin->hasRole('Super_Admin')) {
            $user = user::findorfail($user_id);
            $user->validation = 0;
            $user->save();
            return response()->json(['msg' => 'user has been blocked'], 200);
        }
        return response()->json(['msg' => 'ERROR!'], 500);
    }
    public function addSupplier(Request $request)
    {
      
        $password = $request->input('password');
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->description = $request->input('description');
        $user->password = Hash::make($password);
        $user->contact =$request->input('contact');
        $user->phone_num2 = $request->input('phone_num2');
        $user->tax_number =$request->input('tax_number');
        $user->first_resp_name = $request->input('first_resp_name');
        $user->adress = $request->input('adress');
        $user->country =$request->input('country');
        $user->region = $request->input('region');
        $user->postal_code =$request->input('postal_code');
        $user->longitude = $request->input('lng');
        $user->latitude = $request->input('lat');
        $user->min_price = $request->input('min_price');
        $user->logistic_service = $request->input('logistic_service');
        $user->product_visibility = $request->input('product_visibility');
        if ($request->hasFile('profil_img')) {
            $path = $request->file('profil_img')->store('profils','google');
            $fileUrl = Storage::url($path);
            $user->img_url = $fileUrl;
            $user->img_name = basename($path);
        }
        $role = Role::where('name', 'Supplier')->first();
        $role->users()->save($user);
        if ($user->save()) {
            $commission = new SiyouCommission();
            $commission->supplier_id = $user->id;
            $commission->commission_percent = 0;
            $commission->deposit = 0;
            $commission->Deposit_rest = 0;
            $commission->commission_amount = 0;
            $commission->save();
            return response()->json(["msg" => "user added successfully !"], 200);
        }
        return response()->json(["msg" => "ERROR !"], 500);
    }

    

    public function addShop_Owner(Request $request)
    {
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');
        $user = new User();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $role = Role::where('name', 'Shop_Owner')->first();
        $role->users()->save($user);
        $user->save();
        return response()->json(["msg" => "user added successfully !"], 200);

        return response()->json(["msg" => "ERROR !"], 500);
    }

    public function addSalesManager(Request $request)
    {
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->password = Hash::make($password);

        $role = Role::where('name', 'SalesManager')->first();
        $role->users()->save($user);
        $user->save();
        return response()->json(["msg" => "user added successfully !"], 200);

        return response()->json(["msg" => "Error !"], 500);
    }

    public function getSupplierOrderShop()
    {
        // if (!$user = JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(["msg" => 'user_not_found'], 404);
        // }
        // $data = compact('user');
        // $supplier = User::where('id', $data['user']['id'])->first();
        $supplier = AuthController::me();
        return response()->json($supplier->getShopsThroughOrder()->distinct()->with('salesmanagerToShop')->distinct('salesmanagerToShop')->get(), 200);
    }

    public function getSupplierSalesmanagerShop()
    {
        $supplier = AuthController::me();
        // echo $supplier;
        // return response()->json($supplier->salesmanagerToSupplier()->get());
        $responseData = [];
        $data = $supplier->salesmanagerToSupplier()
            ->with(['shopOwners' => function ($query) use ($supplier) {
                $query->wherePivot('supplier_id', $supplier->id)->distinct();
            }])->distinct()->get();
        foreach ($data as $element) {
            // echo $element;
            if (sizeof($element['shopOwners'])) {
                $result = DB::table('supplier_salesmanager_shop_owner')
                    ->select('commission_amount')
                    ->where([
                        ['salesmanager_id', '=', $element['id']],
                        ['shop_owner_id', '=', $element['shopOwners'][0]->id],
                        ['supplier_id', '=', $supplier->id]
                    ])
                    ->first();
            } else {

                $result = DB::table('supplier_salesmanager_shop_owner')
                    ->select('commission_amount')
                    ->where([
                        ['salesmanager_id', '=', $element['id']],
                        // ['shop_owner_id', '=', $element['shopOwners'][0]->id],
                        ['supplier_id', '=', $supplier->id]
                    ])
                    ->first();
            }
            $element['commission_amount'] = $result->commission_amount;
            $responseData[] = $element;
        }
        return $responseData;
        // return response()->json($supplier->salesmanagerToSupplier()
        // ->with(['shopOwners' => function ($query) use ($supplier) {
        //     $query->wherePivot('supplier_id', $supplier->id)->distinct();
        // }])->distinct()->get());
    }

    public function addSalesManagerToSupplier(Request $request)
    {
        $supplier = AuthController::me();
        $salesManagerId = $request->input('salesmanager_id');
        $salesmanager = User::where('id', $salesManagerId)->first();
        $oldCount = $supplier->salesmanagerToSupplier()->count();
        $supplier->salesmanagerToSupplier()->attach($salesmanager);
        $newCount = $supplier->salesmanagerToSupplier()->count();
        return $newCount > $oldCount ? response()->json(["msg" => "All Is Good"], 200) : response()->json(["msg" => "Error"], 500);
    }

    public static function getUserByEmail($email)
    {
        return User::whereEmail($email)
            ->with('role')->first();
    }

    public function getSalesManagerByEmail(Request $request)
    {
        $supplier = AuthController::me();
        $email = $request->input('email');
        $supplierSalesmanagerList = $supplier->salesmanagerToSupplier()->distinct()->get();
        $salesManagerEmails = array();
        foreach ($supplierSalesmanagerList as $element) {
            $salesManagerEmails[] = $element['email'];
        }
        $salesmanagerList = User::whereNotIn('email', $salesManagerEmails)
            ->where('email', $email)
            ->whereHas('role', function ($q) {
                $q->where('name', 'SalesManager');
            })->get();
        return response()->json($salesmanagerList, 200);
    }


    public function getShopOwnerByEmail(Request $request)
    {
        // $supplier = AuthController::me();
        $email = $request->input('email');
        $shopsIds = $request->input('shopsIds');
        $shopList = User::where('email', $email)
            ->whereNotIn('id', $shopsIds)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Shop_Owner')->distinct();
            })->get();
        return response()->json($shopList, 200);
    }

    public function linkSalesManagerToShop(Request $request)
    {
        $supplier = AuthController::me();
        $salesManagerId = $request->input('salesmanager_id');
        $shop_owner_id = $request->input('shop_owner_id');
        $commission_amount = $request->input('commission_amount');
        // $shop_owner = User::find($shop_owner_id);
        // $salesmanager = User::find($salesManagerId)->first();
        $row = Supplier_Salesmanager_ShopOwner::where([
            "supplier_id" => $supplier['id'],
            "salesmanager_id" => $salesManagerId,
            "shop_owner_id" => null
        ])->first();
        if ($row) {
            $row->shop_owner_id = $shop_owner_id;
            $row->commission_amount = $commission_amount;
            if ($row->save()) {
                return response()->json(["msg" => 'data updated'], 200);
            }
            return response()->json(["msg" => 'erreur while updating'], 500);
        }
        else
        {
            $row = new Supplier_Salesmanager_ShopOwner();
            $row->supplier_id=$supplier->id;
            $row->shop_owner_id=$shop_owner_id;
            $row->salesmanager_id=$salesManagerId;
            $row->commission_amount = $commission_amount;
            if ($row->save()) {
                return response()->json(["msg" => 'data saved'], 200);
            }
            return response()->json(["msg" => 'erreur while saving'], 500);
        }
        return response()->json(['msg' => 'no data found'], 404);
    }

    public function linkShopTosupplier(Request $request)
    {
        $supplier = AuthController::me();
        $shop_owner_id = $request->input('shop_owner_id');
        if($supplier->shop_owners()->where('shop_owner_id',$shop_owner_id)->exists())
        {
            return response()->json(["msg" => 'data already exists'], 200);
        }
        else {
            $row = new Supplier_Salesmanager_ShopOwner();
            $row->supplier_id=$supplier->id;
            $row->shop_owner_id=$shop_owner_id;
            if ($row->save()) {
                return response()->json(["msg" => 'data saved'], 200);
            }
            return response()->json(["msg" => 'erreur while saving'], 500);
        }

    }

    public function getSalesManagerList()
    {
        $supplier = AuthController::me();
        $SMList = $supplier->salesmanagerToSupplier;
        return response()->json(["salesManagers" => $SMList]);
    }


    public function getSupplierList()
    {
        $supplierList = User::whereHas('role', function ($query) {
            $query->where('name', '=', 'Supplier');
        })->get();
        return response()->json($supplierList, 200);
    }
    public function getShopsList()
    {
        $shoplist = User::whereHas('role', function ($query) {
            $query->where('name', '=', 'Shop_Owner ')->orwhere('name', '=', 'Shop_Manager');
        })->get();
        return response()->json($shoplist, 200);
    }
    public function getSalesManagersList()
    {
        $SMList = User::whereHas('role', function ($query) {
            $query->where('name', '=', 'SalesManager');
        })->get();
        return response()->json($SMList, 200);
    }
    public function deleteUser($id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $user = User::find($id);
            $user->delete();
            return response()->json(["msg" => "the user has been deleted successfully !!"]);
        }
        return response()->json(['msg' => 'ERROR', 500]);
    }

    public function ShowUser($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function UsersList()
    {
        $userlist = User::with('role')->get();
        return response()->json($userlist);
    }


    public function GetUserByRole($id)
    {
        $user_role = User::where('role_id', $id)->get();
        return response()->json($user_role);
    }


    public function updateSalesmanagerCommission(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Supplier')) {
            $shop_owner_id = $request->input('shop_owner_id');
            $salesmanager_id = $request->input('salesmanager_id');
            $supplier_id = $user->id;
            $commission_amount = $request->input('commission_amount');
            $updateRow = DB::table('supplier_salesmanager_shop_owner')
                ->where([
                    ['salesmanager_id', '=', $salesmanager_id],
                    ['shop_owner_id', '=', $shop_owner_id],
                    ['supplier_id', '=', $supplier_id]
                ])
                ->update(['commission_amount' => $commission_amount]);
            if ($updateRow) {
                return response()->json(["msg" => "update Success ! "], 200);
            }
        }
        return response()->json(["msg" => "Update Error"]);
    }

    public function searchSupplier(Request $request)
    {
        $key_word = $request->input('key_word');
        $supplier=User::where('first_name', 'like', '%' . $key_word . '%')
                        ->orWhere('last_name', 'like', '%' . $key_word . '%')
                        ->get();
        return response()->json(["data" => $supplier]);
    }

    public function signUp(Request $request)
    {
        // $validator = Validator::make($request->all(), 
        // [ 'email' => 'required|email', 
        //   'password' => 'required|min:6',
        //   'first_name' => 'required', 
        //   'last_name' => 'required', 
        //   'profil_img' => 'image'  ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors());
        // }
        $password = $request->input('password');
        $role = $request->input('role');
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->description = $request->input('description');
        $user->password = Hash::make($password);
        $user->contact =$request->input('contact');
        $user->phone_num2 = $request->input('phone_num2');
        $user->tax_number =$request->input('tax_number');
        $user->first_resp_name = $request->input('first_resp_name');
        $user->adress = $request->input('adress');
        $user->country =$request->input('country');
        $user->region = $request->input('region');
        $user->postal_code =$request->input('postal_code');
        $user->longitude = $request->input('lng');
        $user->latitude = $request->input('lat');
        $user->min_price = $request->input('min_price');
        $user->logistic_service = $request->input('logistic_service');
        $user->product_visibility = $request->input('product_visibility');
        if ($request->hasFile('profil_img')) {
            $path = $request->file('profil_img')->store('profils','google');
            $fileUrl = Storage::url($path);
            $user->img_url = $fileUrl;
            $user->img_name = basename($path);
        }
        $role = Role::where('name', $role)->first();
        if ($role->name == "Shop_Owner")
        {
            $role->users()->save($user);
            $s2c_shop = DB::connection('S2C')->table('users')->insertGetId(
                ["first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "password" => $user->password,
                "contact" => $user->contact,
                "role_id" => 1,
                "activated_account" => 1,
		"created_at"=>Carbon::now(),
		"updated_at"=>Carbon::now()]);
                return response()->json(["shop_owner_id" => $s2c_shop], 200);
        }
        return response()->json(["msg" => "user added successfully !"], 200);

    }
    public function createUsers (Request $request){

        $tmp =User::where('email',$request->input('email'))->orWhere('contact',$request->input('contact'))->first();
        if($tmp) {
            return response()->json(["msg" => "User already exists !!"]);
        }

        if($request->input('role_id') == 3) {
             return $this->signUpShop($request);
        } else {
            $password = $request->input('password');
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->description = $request->input('description');
        $user->password = Hash::make($password);
        $user->contact =$request->input('phone_num1');
        $user->phone_num2 = $request->input('phone_num2');
        $min_price =$request->has('min_price')? $request->input('min_price'):0;
        $logistic_service =$request->has('logistic_service')? $request->input('logistic_service'):0;
        $user->tax_number =$request->input('tax_number');
        $user->first_resp_name = $request->input('first_resp_name');
        $user->adress = $request->input('adress');
        $user->country =$request->input('country');
        $user->region = $request->input('region');
        $user->postal_code =$request->input('postal_code');
        $user->longitude = $request->input('lng');
        $user->latitude = $request->input('lat');
        $user->min_price =$min_price ;
        $user->logistic_service = $logistic_service;
        $user->product_visibility = $request->input('product_visibility');
        if ($request->hasFile('profil_img')) {
            $path = $request->file('profil_img')->store('profils','google');
            $fileUrl = Storage::url($path);
            $user->img_url = $fileUrl;
            $user->img_name = basename($path);
        }
        $role = Role::where('id', $request->role_id)->first();
    
        $role->users()->save($user);
    
        
    
    
        return response()->json(["msg" => "user added successfully with role " .$role->name ], 200);
        }
        
    
    }
    public function signUpShop(Request $request)
    {
       $tmp =User::where('email',$request->input('email'))->orWhere('contact',$request->input('contact'))->first();
        if($tmp) {
            return response()->json(["msg" => "User already exists !!"]);
        }
       $password = $request->input('password');
        $user_role = $request->input('role_id');
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->description = $request->input('description');
        $min_price =$request->has('min_price')? $request->input('min_price'):0;
        $user->password = Hash::make($password);
        //$user->min_price = 0;
        $user->contact =$request->input('contact');
        $user->phone_num2 = $request->input('phone_num2');
        $user->tax_number =$request->input('tax_number');
        $user->first_resp_name = $request->input('first_resp_name');
        $user->adress = $request->input('adress');
        $user->country =$request->input('country');
        $user->region = $request->input('region');
        $user->postal_code =$request->input('postal_code');
        $user->longitude = $request->input('lng');
        $user->latitude = $request->input('lat');
        $user->min_price = 0;
        //$request->input('min_price');
        // $user->logistic_service = $request->input('logistic_service');
        // $user->product_visibility = $request->input('product_visibility');
        if ($request->hasFile('profil_img')) {
            $path = $request->file('profil_img')->store('profils','google');
            $fileUrl = Storage::url($path);
            $user->img_url = $fileUrl;
            $user->img_name = basename($path);
        }
        $role = Role::where('id', $user_role)->first();
            //echo $role;

            $role->users()->save($user);
            //$user->save();
            //echo "saved";
            $s2c_shop = DB::connection('S2C')->table('users')->insertGetId(
                ["first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "password" => $user->password,
                "contact" => $user->contact,
                "hide_cost_price"=>0,
                "role_id" => 1,
                "activated_account" => 1,
		"created_at"=>Carbon::now(),
		"updated_at"=>Carbon::now()]);
            $new_store = DB::connection('S2C')->table('shops')->insertGetId(
                ["store_name" => $request->input('store_name'),
                "store_name_en" => $request->input('store_name_en'),
                "store_name_it" =>  $request->input('store_name_it'),
                "store_area" =>  $request->input('store_area'),
                "store_domain" =>  $request->input('store_domain'),
                "store_adress" => $request->input('store_adress'),
                "contact" => $request->input('store_contact'),
                "store_longitude" => $request->input('store_longitude'),
                "store_latitude" => $request->input('store_latitude'),
                "opening_hour" => $request->input('opening_hour'),
                "closure_hour" => $request->input('closure_hour'),
                "store_ip" => $request->input('store_ip'),
                "store_is_selfsupport" => $request->input('store_is_selfsupport'),
                "shop_owner_id" => $s2c_shop,
		"created_at"=>Carbon::now(),
        "updated_at"=>Carbon::now()]);
        //id, shop_owner_id, max_chains, max_managers, max_cachiers, start_date, finish_date, created_at, updated_at, max_operators
        $new_license = DB::connection('S2C')->table('licenses')->insertGetId(
            [
            "shop_owner_id"=>$s2c_shop,
            "max_chains"=>1,
            "max_managers"=>3,
            "max_operators"=>3,
            "max_cachiers"=>3,
            "start_date"=>date('Y-m-d'),
            "finish_date"=>date('Y-m-d', strtotime('+1 year')),
            "created_at"=>Carbon::now(),
            "updated_at"=>Carbon::now()
            ]);
                
          
        
        return response()->json(["msg" => "user added successfully !"], 200);

    }
   public function updateShopOwner(Request $request,$id)
    {
	$user = AuthController::me();
	$user_email = $user->email;
       //$tmp =User::where('email',$request->input('email'))->orWhere('contact',$request->input('contact'))->first();
       $shop_owner =DB::connection('S2C')->table('users')->where('id',$id)->first();
        $email = $shop_owner->email;
	if($user_email != $email) {
		//return response()->json(['code'=>0,'msg'=>'not allowed']);
        }
        $user = User::where('email',$email)->first();
        if(!$shop_owner || !$user) {
            return response()->json(["msg" => "User not found !!"]);
        }
       $password = $request->input('password');
        //user_role = $request->input('role_id');
        //$user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        //$user->description = $request->input('description');
        //$min_price =$request->has('min_price')? $request->input('min_price'):0;
        //$user->password = Hash::make($password);
      
        $user->adress = $request->input('adress');
        $user->country =$request->input('country');
	$user->contact =$request->input('contact');
        //$user->region = $request->input('region');
        //$user->postal_code =$request->input('postal_code');
        //$user->longitude = $request->input('lng');
        //$user->latitude = $request->input('lat');
        $user->min_price = 0;
       
        // if ($request->hasFile('profil_img')) {
        //     $path = $request->file('profil_img')->store('profils','google');
        //     $fileUrl = Storage::url($path);
        //     $user->img_url = $fileUrl;
        //     $user->img_name = basename($path);
        // }
        //$role = Role::where('id', $user_role)->first();
           // echo $role;

            //$role->users()->save($user);
            $user->save();
            //echo "saved";
          DB::connection('S2C')->table('users')->where('id',$id)->update(
                ["first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                //"password" => $user->password,
                "contact" => $user->contact,
		 "billing_address_1"=>$request->input('billing_address_1'),
      "billing_country"=> $request->input('billing_country'),
      "billing_city"=>$request->input('billing_city'), 
      "billing_postal_code"=>$request->input('billing_postal_code') ,               
                //"min_price"=>0,
                "role_id" => 1,
                "activated_account" => 1,]);
                //echo $s2c_shop;
            
                
          
        
        return response()->json(["msg" => "user updated successfully !"], 200);

    }

}
