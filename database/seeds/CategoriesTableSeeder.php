<?php

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/SQLFiles/Categories.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Categories table seeded!');
        // Main Categories
        // $mainCategory1 = new Category();
        // $mainCategory1->category_name = 'Electronics';
        // $mainCategory1->save();

        // $mainCategory2 = new Category();
        // $mainCategory2->category_name = 'Fashion';
        // $mainCategory2->save();

        // Sub Categories
        // $subCategory1 = new Category();
        // $subCategory1->category_name = 'Computers';
        // $subCategory1->parent_category_id = $mainCategory1->id;
        // $subCategory1->save();
    }
}
