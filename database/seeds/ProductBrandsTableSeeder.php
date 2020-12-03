<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/ProductBrands.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Product brands table seeded!');
    }
}
