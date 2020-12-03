<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // public function getImageUrl(Request $request)
    // {
    //     $name = $request->query('name');
    //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    //     $path = Storage::url($name);
    //     return response()->json(["msg" => $storagePath . $path]);
    // }

    public function addProduct(Request $request)
    {
        $supplier = AuthController::me();
        $product = new Product();
        $catalog = Catalog::find(1);
        $product->product_name = $request->input('product_name');
        $product->product_description = $request->input('product_description');
        $product->quantity = $request->input('quantity');
        $product->product_price = $request->input('product_price');
        $product->product_weight = $request->input('product_weight');
        $product->product_size = $request->input('product_size');
        $product->product_color = $request->input('product_color');
        $product->supplier_id= $supplier->id;
        $product->category_id = $request->input('category_id');
        if ($request->hasFile('product_image')) {
            $path = $request->file('product_image')->store('products', 'google');
            $fileUrl = Storage::url($path);
            // $fileUrl = $disk->url($path);
            // return response()->json(["msg" => "has file"]);
            $product->product_image = $fileUrl;
        }
        // return response()->json(["msg" => "has no file"]);
        if ($product->save()) {
            $product->catalogs()->attach($catalog);
            return response()->json($product);
        }
        return response()->json(["msg" => "error while saving"]);
    }
    public function GetAllSupplierWithProducts()
    {
        $Supplier_products = User::with('products')->where('role_id', 2)->get();
        return response()->json($Supplier_products);
    }

    public function GetAllCategoryWithProducts()
    {
        $category_products = Category::with('products')->get();
        $allCategoryProduct = $category_products[0];


        foreach ($allCategoryProduct['products'] as $product) {
            $product['suppliers'] = User::where('id', $product['supplier_id'])->get();
        }
        return response()->json($allCategoryProduct);
    }


    public function showProduct($id)
    {
        $product = Product::findorfail($id);
        return response()->json($product);
    }

    public function productList()
    {
        $productsList = Product::with('supplier')->with('category')->get();
        return response()->json($productsList, 200);
    }

    public function getFilteredProductsList(Request $request)
    {
        // if (($request->has('supplier') && $request->query('supplier')) && ($request->has('category') && $request->query('category'))) {
        //     return response()->json(["data" => "there is data"]);
        // } else {
        //     return response()->json(["data" => "no data"]);
        // }
        if (($request->has('supplier') && $request->query('supplier')) && ($request->has('category') && $request->query('category'))) {
            $productsList = Product::where([
                ["supplier_id", $request->get('supplier')],
                ["category_id", $request->get('category')]
            ])->with('supplier')->get();
        } else if ($request->has('supplier') && $request->query('supplier')) {
            $productsList = Product::where("supplier_id", $request->get('supplier'))->with('supplier')->get();
        } else if ($request->has('category') && $request->query('category')) {
            $productsList = Product::where("category_id", $request->get('category'))->with('supplier')->get();
        } else {
            $productsList = Product::with('supplier')->get();
        }
        return response()->json($productsList, 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findorfail($id);
        $product->product_name = $request->input('product_name');
        $product->product_description = $request->input('product_description');
        $product->quantity = $request->input('quantity');
        $product->product_price = $request->input('product_price');
        $product->product_weight = $request->input('product_weight');
        $product->product_size = $request->input('product_size');
        $product->product_color = $request->input('product_color');
        $product->product_package = $request->input('product_package');
        $product->product_box = $request->input('product_box');
        $product->product_barcode = $request->input('product_barcode');
        $product->discount_type = $request->input('discount_type');
        $product->product_discount_price = $request->input('product_discount_price');
        $product->category_id = $request->input('category_id');
        //$product->supplier_id=Auth::user()->id;
        $product->supplier_id = $request->input('supplier_id');
        $product->product_image = $request->input('product_image');
        if ($product->save()) {
            return response()->json($product);
        }
        return response()->json(["msg" => "ERROR !!"]);
    }

    public function getProductByCategory($id)
    {
        $category_products = Category::with('products')->find($id);
        foreach ($category_products['products'] as $product) {
            $product['suppliers'] = user::where('id', $product['supplier_id'])->get();
        }
        return response()->json($category_products);
    }

    public function getProductBySupplier($id)
    {
        $supplier_products = User::with('products')->find($id);
        foreach ($supplier_products['products'] as $product) {
            $product['categories'] = Category::where('id', $product['category_id'])->get();
        }
        return response()->json($supplier_products);
    }

    public function getProductsBySupplier()
    {
        $supplier = AuthController::me();
        $response = array();
        $response['products'] = $supplier->products()->with('category')->get();
        $response['maxPrice'] = $supplier->products()->max('product_price');
        $response['minPrice'] = $supplier->products()->min('product_price');
        return response()->json($response, 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json(["msg" => "the Product has been deleted Successfully !!"]);
    }

    public function addProductsWithExcel(Request $request)
    {
        // if ($request->hasFile('products')) {
        //     return response()->json(["msg" => "has file!"]);
        // }
        $file = Input::file('products');
        $fileReader = IOFactory::createReaderForFile($file);
        $spreadSheet = $fileReader->load($file->path());
        $content = $spreadSheet->getActiveSheet()->toArray();
        $responseArray = [];
        foreach ($content as $key => $value) {
            if ($key === 0) continue;
            $product = new Product();
            foreach ($value as $key => $val) {
                $product[$content[0][$key]] = $val;
            }
            if ($product->save()) {
                $responseArray[] = $product;
            }
        }
        // return response()->json($spreadSheet->getActiveSheet()->toArray());
        return response()->json($responseArray);
        //  test
        // $inputFile = __dir__ .'/products.xlsx';
        // $spreadsheet = IOFactory::load($inputFile);
        // return response()->json($spreadsheet->getActiveSheet()->toArray());
    }

    public function getSalesmanagerProducts() {
        $salesmanager = AuthController::me();
        $supplierList = $salesmanager->suppliers;
        $suppliersIds = [];
        forEach($supplierList as $supplier) {
            $suppliersIds[] = $supplier->id;
        }
        // $productList = $salesmanager->suppliers()->with('products')->get();
        $productList = DB::table('products')->whereIn('supplier_id', $suppliersIds)->get();
        return response()->json($productList, 200);
    }
}
