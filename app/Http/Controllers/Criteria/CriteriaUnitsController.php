<?php namespace App\Http\Controllers\Criteria;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\CriteriaBase;
use App\Models\CriteriaUnit;
use App\Models\Category;

use Exception;
use Illuminate\Http\Request;

class CriteriaUnitsController extends Controller {

    
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addCriteriaUnit(Request $request)
    {
        $criteria_id = $request->input('criteria_id');
        $criteriaUnits = $request->input('units');

        foreach($criteriaUnits as $unit)
        {
            $criteriaUnit = new CriteriaUnit ;
            $criteriaUnit->unit_name = $unit;
            $criteriaUnit->criteria_base_id = $criteria_id;
            // $criteriaUnit->criteria_base_id = 0;
            $criteriaUnit->save();
        }
        return response()->json(['msg'=>'criteria unit has been saved'],200);
    }

    public function updateUnit(Request $request, $id)
    {
        $unit_name = $request->input('unit_name');
        $criteriaUnit = CriteriaUnit::find($id);
        $criteriaUnit->unit_name = $unit_name;
        if($criteriaUnit->save()){
            return response()->json(['msg'=>'criteria unit has been saved'],200);
            }
        
        return response()->json(['msg'=>'Error'],500);
       
    }

    
    public function deleteUnit(Request $request, $id)
    {
        $criteria_unit = CriteriaUnit::find($id);
        // $criteria_unit->CriteriaBase()->detach($id);
        if ($criteria_unit->delete()) {
                return response()->json(['msg'=>'Success'],200);
           }
        return response()->json(['msg'=>'Error!!'],500);   
    }

    public function getUnit(Request $request)
    {
        $unit_id = $request->input('unit_id');
        $criteria_unit = CriteriaUnit::find($unit_id);
        return response()->json($criteria_unit,200); 
    }


}
