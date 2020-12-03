<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class WarehousesController extends Controller
{

    public function addWarehouse(Request $request)
    {
        $user = AuthController::me();
        $latitude = $request->input('choose the warehouse location latitude');
        $longitude = $request->input('choose the warehouse location longitude');
        $storage_space = $request->input('enter the warehouse storage limit');
        $picking_process = $request->input('enter the time spent picking orders');
        $prompt_delivery = $request->input('enter the shipment tracking process');
        $warehouse = new Warehouse();
        $warehouse->latitude = $latitude;
        $warehouse->longitude = $longitude;
        $warehouse->storage_space = $storage_space;
        $warehouse->picking_process = $picking_process;
        $warehouse->prompt_delivery = $prompt_delivery;
        $warehouse->user_id = $user->id;
        $warehouse->save();
        return response()->json(["msg" => "warehouse has been added successfully"], 200);
        return response()->json(["msg" => "ERROR!"], 500);
    }
    public function AddProductstowarehouse(Request $request, $id)
    {
        $user = AuthController::me();

        $warehouse = Warehouse::findorfail($id);
        $product_id = $request->input('products');
        $products = Product::find($product_id);
        $quantity = $request->input('quantity');
        foreach ($products as $product) {
            $warehouse->products()->sync([$products, ['quantity' => $products['quantity']], ['user_id' => $warehouse->user_id]]);
        }
        return response()->json(["msg" => "products added to warehouse successfully !!"], 200);
        return response()->json(["msg" => "ERROR"], 500);
    }
}
