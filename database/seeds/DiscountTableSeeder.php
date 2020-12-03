<?php

use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $discount= new discount();
        $discount->type = 'discount percent';
        $discount->save();

        // $discount1= new discount();
        // $discount1->type = 'discount n by m';
        // $discount1->save();

        $discount2= new discount();
        $discount2->type = 'discount amount';
        $discount2->save();

        $discount3= new discount();
        $discount3->type = 'discount fix price';
        $discount3->save();
    }
}
