<?php namespace App\Http\Controllers;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentMethod;

class PaymentMethodsController extends Controller {

    public function __construct()
    {
        //$this->middleware('auth:api');
    }
    public function getPaymentMethodList(){

        $paymentList = PaymentMethod::all();
        return response()->json(["paymentList" => $paymentList]);

    }

}
