<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class ProductBasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(1,9) as $index) {
            DB::table('product_base')->insert([
                'product_name' => 'product'.$index,
                'product_description' => 'Product description' .$index,
                'category_id' => $faker->numberBetween($min = 1, $max = 6),
                'taxe_rate'=> 0.22,
                'supplier_id'=> $faker->randomElement([1,2,3]),
                'brand_id'=> $faker->randomElement([1,2,3,4])
                ]);
        }
    }
}
