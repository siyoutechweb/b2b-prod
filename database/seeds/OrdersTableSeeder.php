<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/Orders.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Orders table seeded!');
    }
}
