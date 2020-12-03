<?php

namespace App\Http\Controllers\statut;
use App\Http\Controllers\Controller;
use App\Models\Statut;
use Illuminate\Http\Request;

class StatutsController extends Controller
{
    public function addStatut(Request $request)
    {
        $statut = new Statut();
        $statut->statut_name = $request->input('statut_name');
        $statut->description = $request->input('description');
        if ($statut->save()) {
            return response()->json("Statut has been added Successfully !!");
        }
        return response()->json("Error !!");
    }
    public function ShowStatut($id)
    {

        $statut = Statut::findorfail($id);
        return response()->json($statut);
    }
    public function Statutlist()
    {
        $statutlist = Statut::all();
        return response()->json($statutlist);
    }
    public function DeleteStatut($id)
    {
        $statut = Statut::find($id);
        $statut->Delete();
        return response()->json(["msg" => "the Statut has been deleted !!"]);
    }
    public function UpdateStatut(Request $request, $id)
    {
        $statut = Statut::findorfail($id);
        $statut->statut_name = $request->input('statut_name');
        $statut->description = $request->input('description');
        if ($statut->save()) {
            return response()->json($statut);
        }
        return response()->json(["msg" => "ERROR !!"]);
    }
}
