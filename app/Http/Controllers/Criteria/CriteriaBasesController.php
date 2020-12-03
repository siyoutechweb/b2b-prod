<?php

namespace App\Http\Controllers\Criteria;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\CriteriaBase;
use App\Models\CriteriaUnit;
use App\Models\Category;

use Exception;
use Illuminate\Http\Request;

class CriteriaBasesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addCriteria(Request $request)
    {
        $criteria = $request->input('criteria_name');
        $criteriaUnits = $request->input('units');

        $criteriaBase = new CriteriaBase;
        $criteriaBase->name = $criteria;
        $criteriaBase->save();
        if ($request->filled('categories_id')) {
            $categories_id = $request->input('categories_id');
            foreach ($categories_id as $category_id) {
                $category = category::find($category_id);
                $criteriaBase->Categories()->attach($category);
            }
        }
        if (!empty($criteriaUnits)) {
            foreach ($criteriaUnits as $unit) {
                $criteriaUnit = new CriteriaUnit;
                $criteriaUnit->unit_name = $unit;
                $criteriaUnit->criteria_base_id = $criteriaBase->id;
                $criteriaUnit->save();
            }
        }

        return response()->json(['msg' => 'criteria base has been saved'], 200);
    }

    // public function addCriteria(Request $request)
    // {
    //     $criteria = $request->input('criteria_name');
    //     $Unit_ids = $request->input('Units_id');
    //     $categories_id = $request->input('categories_id');

    //     $criteriaBase = new CriteriaBase;
    //     $criteriaBase->name = $criteria;
    //     $criteriaBase->save();
    //     if (!empty($categories_id)) {
    //         $criteriaBase->Categories()->attach($categories_id);
    //     }
    //     if (!empty($Unit_ids)) {
    //         $criteriaBase->criteriaUnit()->attach($Unit_ids);
    //         }

    //     return response()->json(['msg' => 'criteria base has been saved'], 200);
    // }

    public function getCriteriaList(Request $request)
    {
        if ($request->filled('category_id')) {
            $category_id = $request->input('category_id');
            $criteriaList = Category::with(['CriteriaBase' => function ($query) {
                $query->with('CriteriaUnit')->get();
                $query->with('productItems:criteria_unit_id,criteria_value')->get();
            }])->find($category_id);
        } else {
            $criteriaList = CriteriaBase::with('CriteriaUnit','Categories')->get();
        }
        return response()->json($criteriaList, 200);
    }

    public function getCategoriesCriteria()
    {
        $categories = Category::with('CriteriaBase')->whereNotNull('parent_category_id')
                                ->orderBy('parent_category_id')->get();
        return response()->json($categories);
    }

    public function getCriteria(Request $request)
    {
        $criteria_id = $request->input('criteria_id');
        $criteriaList = CriteriaBase::with('CriteriaUnit','Categories')->find($criteria_id);
        
        return response()->json($criteriaList,200);
    }

    public function updateCriteria(Request $request, $id)
    {
        $criteria_name = $request->input('criteria_name');
        $criteriaUnits = $request->input('units');
        // $Unit_ids = $request->input('Units_id');
        $categories_id = $request->input('categories_id');
        $criteriaBase = CriteriaBase::find($id);
        $criteriaBase->name = $criteria_name;

        $criteriaBase->save();
        if (!empty($categories_id)) {
            $criteriaBase->Categories()->sync($categories_id);
        }
        // if (!empty($Unit_ids)) {
        //     $criteriaBase->CriteriaUnit()->sync($Unit_ids);
        // }
        if (!empty($criteriaUnits)) {
            foreach ($criteriaUnits as $units) {
                $unit = (object) $units;
                $criteriaUnit = CriteriaUnit::find($unit->id);
                $criteriaUnit->unit_name = $unit->unit_name;
                $criteriaUnit->save();
            }
        }
        return response()->json(['msg' => 'criteria base has been saved'], 200);
    }


    public function deleteCriteria(Request $request, $id)
    {
        // $criteria_id = $request->input('criteria_id');
        $criteria = CriteriaBase::find($id);
        $criteria->Categories()->detach();
        $criteria->CriteriaUnit()->delete();
        // $criteria->CriteriaUnit()->detach();
        if ($criteria->delete()) {
            return response()->json(['msg' => 'Success'], 200);
        }
        return response()->json(['msg' => 'Error'], 500);
    }

    
}
