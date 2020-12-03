<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
class CriteriaCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(1,10) as $index) {
            DB::table('categories_criteria')->insert([
                'category_id'=> $faker->numberBetween($min = 1, $max = 30),
                'criteria_id'=> $faker->numberBetween($min = 1, $max = 2)
                ]);
        }
    }
}
