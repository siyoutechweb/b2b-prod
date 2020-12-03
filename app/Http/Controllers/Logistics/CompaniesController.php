<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{

    public function AddCompany(Request $request)
    {

        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $company = new Company();
            $company->name = $request->input('name');
            $company->description = $request->input('description');
            $company->email = $request->input('email');
            $company->phone = $request->input('phone');
            $company->adress = $request->input('adress');
            $company->save();
            return response()->json(['msg' => 'Company has been added successfully'], 200);
        }
        return response()->json(['msg' => 'ERROR!'], 500);
    }
    public function UpdateCompany($id, Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $company = Company::find($id);
            $company->name = $request->input('name');
            $company->description = $request->input('description');
            $company->email = $request->input('email');
            $company->phone = $request->input('phone');
            $company->adress = $request->input('adress');
            $company->save();
            return response()->json(['msg' => 'Company has been Updated successfully'], 200);
        }
        return response()->json(['msg' => 'ERROR!'], 500);
    }
    public function DeleteCompany($id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $company = Company::find($id);
            $company->delete();
            return response()->json('Deleted');
        }
        return response()->json('error');
    }

    public function GetCompany($id)
    {
        $company = Company::find($id);
        return response()->json($company);
    }
    public function GetCompaniesList()
    {
        $companies_list = Company::all();
        return response()->json($companies_list);
    }
    public function getmostusedcompany()
    {
        $user = AuthController::me();
        $company = Company::select(
            'companies.*',
            DB::raw('SUM(orders.company_id) as Used')
        )
            ->join('orders', 'companies.id', 'orders.company_id')
            ->groupBy('company_id')->ORDERBY('Used', 'DESC')->LIMIT(1)
            ->get();
        return response()->json($company);
        return response()->json("ERROR!!");
    }
}
