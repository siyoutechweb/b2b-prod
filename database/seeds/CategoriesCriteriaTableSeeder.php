<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesCriteriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/CategoriesCriteria.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Categories criteria table seeded!');
    }
}
