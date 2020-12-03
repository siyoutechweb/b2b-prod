<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentmethod1 = new PaymentMethod();
        $paymentmethod1->name = 'Credit Card';
        $paymentmethod1->save();
        $paymentmethod2 = new PaymentMethod();
        $paymentmethod2->name = 'Paypal';
        $paymentmethod2->save();
        $paymentmethod3 = new PaymentMethod();
        $paymentmethod3->name = 'Bank Transfers';
        $paymentmethod3->save();
        $paymentmethod4 = new PaymentMethod();
        $paymentmethod4->name = 'Bank check';
        $paymentmethod4->save();
        $paymentmethod5 = new PaymentMethod();
        $paymentmethod5->name = 'payment on delivery';
        $paymentmethod5->save();

        
       
    }
}
