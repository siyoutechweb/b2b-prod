<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class ProductItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/ProductItems.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Product items table seeded!');
        // $faker = Factory::create();
        // foreach (range(1,20) as $index) {
        //     DB::table('product_items')->insert([
        //         'item_barcode' => $faker->numberBetween($min = 2000000000000, $max = 2000999999999),
        //         'item_quantity' => $faker->numberBetween($min = 20, $max = 69),
        //         'item_offline_price'=> $faker->randomElement([1000,220,9869,247]),
        //         'item_online_price'=> $faker->randomElement([null,220,null,247]),
        //         'item_quantity' => $faker->numberBetween($min = 20, $max = 69),
        //         'product_base_id' => $faker->numberBetween($min = 1, $max =9)
        //         ]);
        // }
    }
}
