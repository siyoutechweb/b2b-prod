<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriaBaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/CriteriaBase.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Criteria base table seeded!');
    }
}
