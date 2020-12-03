<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class BrandTableSeeder extends Seeder
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
            DB::table('product_brands')->insert([
                'brand_name' => 'brand'.$index
                ]);
        }
    }
}
