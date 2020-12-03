<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tarif;

class TarifsController extends Controller
{

    public function addTarif(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $tarif = new Tarif();
            $tarif->company_id = $request->input('company_id');
            $tarif->kg = $request->input('kg');
            $tarif->price = $request->input('price');
            $tarif->save();
            return response()->json(["msg" => "success!!"]);
        }
        return response()->json(["msg" => "error!!"]);
    }
    public function updateTarif($id, Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $tarif = Tarif::findOrFail($id);
            $tarif->company_id = $request->input('company_id');
            $tarif->kg = $request->input('kg');
            $tarif->price = $request->input('price');
            $tarif->save();
            return response()->json(["msg" => "success!!"], 200);
        }
        return response()->json(["msg" => "error!!"], 500);
    }
    public function DeleteTarif($id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $tarif = Tarif::findorfail($id);
            $tarif->delete();
            return response()->json("tarif has been Deleted Successfully !");
        }
        return response()->json("ERROR !!");
    }
    public function DeleteTarifbycompany($id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $tarif = Tarif::where('company_id', $id);
            $tarif->delete();
            return response()->json("tarifs of company has been Deleted Successfully !");
        }
        return response()->json("ERROR !!");
    }
    public function getTarifbycompany($id)
    {
        $tarif = Tarif::where('company_id', $id)->get();
        return response()->json($tarif);
        return response()->json("ERROR !!");
    }
    public function getTarifbyweight($kg, $id)
    {
        $tarif = Tarif::where('kg', $kg)->where('company_id', $id)->get();
        return response()->json($tarif);
        return response()->json("ERROR !!");
    }
    public function getTarifbyweightrange($min_kg, $max_kg, $id)
    {
        $tarif = Tarif::where('kg', '>=', $min_kg)->where('kg', '<=', $max_kg)->where('company_id', $id)->get();
        return response()->json($tarif);
        return response()->json("ERROR !!");
    }
    public function Gettarif($id)
    {

        $tarif = Tarif::find($id);
        return response()->json($tarif);
    }
    public function gettarifs_list()
    {

        $tarifs_list = Tarif::all();
        return response()->json($tarifs_list);
    }
}
