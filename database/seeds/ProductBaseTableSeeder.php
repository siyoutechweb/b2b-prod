<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/ProductBase.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Product base table seeded!');
    }
}
