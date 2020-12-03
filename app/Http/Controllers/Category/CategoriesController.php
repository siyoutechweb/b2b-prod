<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Request;

class CategoriesController extends Controller
{

    // public function getCategoryList($id) {
    //     $subCat = Category::find($id);
    //     $parentCat = $subCat->getParentCategory;

    //     return response()->json($parentCat);
    // }


    public function getCategories()
    {
        $categories = Category::whereNull('parent_category_id')->with('subCategories')->get();
        return response()->json($categories);
    }
    public function getMobileCategories()
    {
        $categories = Category::whereNull('parent_category_id')->with('subCategories')->get();
        $categroyList = array();
        $categoryList ['categories'] = $categories;
        return response()->json($categoryList);
    }



    public function deleteCategory($id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $category = Category::find($id);
            $category->Delete();
            return response()->json(["msg" => "the category has been deleted !!"]);
        }
    }


    public function updateCategory(Request $request, $id)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $category = Category::findorfail($id);
            $category->category_name = $request->input('category_name');
            $category->category_cn = $request->input('category_cn');
            $category->category_it = $request->input('category_it');
            $category->category_fr = $request->input('category_fr');
            $category->parent_category_id = $request->input('parent_category_id');
            if ($category->save()) {
                return response()->json($category);
            }
            return response()->json(["msg" => "ERROR !!"]);
        }
    }
    public function addCategory(Request $request)
    {
        $user = AuthController::me();
        if ($user->hasRole('Super_Admin')) {
            $category = new Category();
            $category->category_name = $request->input('category_name');
            $category->category_cn = $request->input('category_cn');
            $category->category_it = $request->input('category_it');
            $category->category_fr = $request->input('category_fr');
            $category->parent_category_id = $request->input('parent_category_id');
	    if ($request->hasFile('category_image')) {
                $path = $request->file('category_image')->store('categories', 'google');
                $fileUrl = Storage::url($path);
                $category->img_url = $fileUrl;
		$user->img_name = basename($path);

            }

            $category->save();
            return response()->json("The Category has been added Successfully !!");
        }
        return response()->json("Error !!");
    }
    public function getSupplierCategories()
    {
        $supplier = AuthController::me();
        $suppCategoryList = $supplier->getSupplierCategoryThroughProduct()->distinct()->orderBy('parent_category_id')->get();
        $parentCategoryIds = $supplier->getSupplierCategoryThroughProduct()->distinct()->pluck('parent_category_id');
        $categoryIds = array();
        foreach ($suppCategoryList as $category) {
            $categoryIds[] = $category->id;
        }
        $categoryList = Category::whereIn('id', $parentCategoryIds)->with(['getChildCategories' => function ($query) use ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        }])->get();
        return response()->json($categoryList, 200);
    }
    public function getSupplierCategory($supplier_id)
    {
        $supplier = user::find($supplier_id);
        $categoryIds = $supplier->getCategoryThroughProduct()->distinct()->pluck('categories.id');
        $parentCategoryIds = $supplier->getCategoryThroughProduct()->distinct()->pluck('parent_category_id');
        $categoryList = Category::whereIn('id', $parentCategoryIds)->with(['getChildCategories' => function ($query) use ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        }])->get();
        $categories['categories'] = $categoryList;
        return response()->json($categories, 200);
    }


    public function getCategoryParent($id)
    {
        $subCat = Category::Find($id);
        $parentCat = $subCat->getParentCategory;

        return response()->json($parentCat);
    }

    public function getCategoryChild($id)
    {
        $parentCat = Category::find($id);
        $subCat = $parentCat->getChildCategories;
        return response()->json($subCat);
    }

    public function showCategory($id)
    {
        // echo Category::findOrFail($id)->with('subCategories')->get();
        $category = Category::findOrFail($id);
        $category['sub_categories'] = $category->subCategories;
        // echo $category->with('subCategories')->get();
        return response()->json($category);
    }

    public function getmostusedcategories()
    {
        $user = AuthController::me();
        $category = Category::select(
            'categories.*',
            DB::raw('SUM(products.category_id) as Used')
        )
            ->join('products', 'categories.id', 'products.category_id')
            ->groupBy('category_id')->ORDERBY('Used', 'DESC')->LIMIT(5)
            ->get();
        return response()->json($category);
        return response()->json("ERROR!!");
    }
    public function addCriteria(Request $request)
    {
        $criterias = $request->input('criteria_ids');
        $category_id = $request->input('category_id');
        $category = category::find($category_id);
        $category->criteriaBase()->attach($criterias);
        return response()->json(["msg"=>'Criteria has been added'],200);
    }

    public function deleteCriteria(Request $request,$categ_id,$crit_id)
    {
        // $criteria = $request->input('criteria_id');
        // $category_id = $request->input('category_id');
        $category = category::find($categ_id);
        $category->criteriaBase()->detach($crit_id);
        return response()->json(["msg"=>'Criteria has been deleted'],200);
    }
}
