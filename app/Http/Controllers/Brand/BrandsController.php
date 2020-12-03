<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth:api');
    }
    public function getBrandList(Request $request)
    {
        $brandList = ProductBrand::all();
        return response()->json($brandList, 200);
    }

    public function getMobileBrandList(Request $request)
    {
        $brand = ProductBrand::all();
        $brandList = array();
        $brandList['brands'] = $brand;
        return response()->json($brandList, 200);
    }

    public function getBrand(Request $request)
    {
        $brand_id = $request->input('brand_id');
        $brand = ProductBrand::find($brand_id);
        return response()->json($brand, 200);
    }

    public function addBrand(Request $request)
    {
        $brand = new ProductBrand;
        $brand->brand_name = $request->input('brand_name');
        if ($request->hasFile('brand_logo')) {
            $path = $request->file('brand_logo')->store('brands', 'google');
            $fileUrl = Storage::url($path);
            $brand->brand_logo = $fileUrl;
        }
        if ($brand->save()) {
            return response()->json(['msg' => 'brand has been saved successfully']);
        }
        return response()->json(['msg' => 'Error !!'], 500);
    }

    public function updateBrand(Request $request, $id)
    {
        $brand = ProductBrand::find($id);
        if ($request->hasFile('brand_logo')) {
            // Storage::disk('google')->delete($brand->brand_logo);
            $path = $request->file('brand_logo')->store('brands', 'google');
            $fileUrl = Storage::url($path);
            $brand->brand_logo = $fileUrl;
        }
        $brand->brand_name = $request->input('brand_name');
        if ($brand->save()) {
            return response()->json(['msg' => 'brand has been updated successfully']);
        }
        return response()->json(['msg' => 'Error !!'], 500);
    }

    public function deleteBrand(Request $request, $id)
    {
        // $brand_id = $request->input('brand_id');
        $brand = ProductBrand::find($id);
        if ($brand->delete()) {
            return response()->json(['msg' => 'brand has been removed'], 200);
        }
        return response()->json(['msg' => 'Error !!'], 500);
    }
}
