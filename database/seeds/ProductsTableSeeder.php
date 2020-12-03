<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facade

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/Products.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Products table seeded!');
    }
}
