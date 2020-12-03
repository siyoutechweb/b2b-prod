<?php

use App\Models\Catalog;
use Illuminate\Database\Seeder;

class CatalogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $catalog = new Catalog();
        $catalog->catalog_name = 'default Catalog';
        $catalog->catalog_description= 'this is a default catalog';
        $catalog->save();
    }
}
