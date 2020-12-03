<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
     {
    //     $this->call(RolesTableSeeder::class);
    //     $this->call(UsersTableSeeder::class);
        //  $this->call(CategoriesTableSeeder::class);
        //  $this->call(CatalogsTableSeeder::class);
        //  $this->call(StatutsTableSeeder::class);
        //  $this->call(ProductsTableSeeder::class);
        //  $this->call(CompaniesTableSeeder::class);
        //  $this->call(TarifsTableSeeder::class);
        //  $this->call(OrdersTableSeeder::class);
        //  $this->call(BrandTableSeeder::class);
        //  $this->call(CriteriaBaseTabeSeeder::class);
        // $this->call(PaymentMethodsTableSeeder::class);
        $this->call(ProductBaseTableSeeder::class);
         $this->call(ProductItemsTableSeeder::class);
        $this->call(CategoriesCriteriaTableSeeder::class);
        $this->call(CriteriaCategoryTableSeeder::class);
         $this->call(ItemCriteriaTableSeeder::class);
        $this->call(DiscountTableSeeder::class);
    }
}
