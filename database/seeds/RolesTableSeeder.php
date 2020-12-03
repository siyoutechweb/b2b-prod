<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') == 'production') {
            $path = 'database/seeds/SQLFiles/Roles.sql';
            DB::unprepared(file_get_contents($path));
            $this->command->info('Roles table seeded!');
        } else {
            $role = new Role();
            $role->name = 'SalesManager';
            $role->save();
            $role1 = new Role();
            $role1->name = 'Supplier';
            $role1->save();
            $role2 = new Role();
            $role2->name = 'Shop_Owner';
            $role2->save();
            $role3 = new Role();
            $role3->name = 'Shop_Manager';
            $role3->save();
            $role4 = new Role();
            $role4->name = 'Super_Admin';
            $role4->save();
        }
    }
}
