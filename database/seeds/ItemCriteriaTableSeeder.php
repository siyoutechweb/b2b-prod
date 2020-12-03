<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
class ItemCriteriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(1,6) as $index) {
            DB::table('items_criteria')->insert([
                'product_item_id'=> $faker->numberBetween($min = 1, $max = 15),
                'criteria_id'=> $faker->numberBetween($min = 1, $max = 2),
                'criteria_unit_id'=> $faker->numberBetween($min = 1, $max = 4),
                'criteria_value'=> 'value'.$index
                ]);
        }
    }
}
