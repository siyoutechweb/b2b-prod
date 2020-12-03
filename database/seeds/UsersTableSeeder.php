<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') == 'production') {
            $path = 'database/seeds/SQLFiles/Users.sql';
            DB::unprepared(file_get_contents($path));
            $this->command->info('Users table seeded!');

        } else {
            $user = new User();
            $user->first_name = 'Baha';
            $user->last_name = 'Bouslama';
            $user->email = 'bahaeddineb@outlook.fr';
            $user->password = Hash::make('siyou2020');
            $user->product_visibility=true;
            $user->save();
            // get role and assign it to a user
            $role = Role::where('name', 'Supplier')->first();
            $role->users()->save($user);


            $user1 = new User();
            $user1->first_name = 'habiba';
            $user1->last_name = 'boujmil';
            $user1->email = 'habiba@outlook.fr';
            $user1->password = Hash::make('siyou2020');
            $user->product_visibility= false;
            $user1->save();
            // get role and assign it to a user
            $role1 = Role::where('name', 'Supplier')->first();
            $role1->users()->save($user1);

            $user9 = new User();
            $user9->first_name = 'ahmed';
            $user9->last_name = 'ben ahmed';
            $user9->email = 'ahmed@outlook.fr';
            $user9->password = Hash::make('siyou2020');
            $user->product_visibility= false;
            $user9->save();
            // get role and assign it to a user
            $role9 = Role::where('name', 'Supplier')->first();
            $role9->users()->save($user9);


            $user2 = new User();
            $user2->first_name = 'marwa';
            $user2->last_name = 'missaoui';
            $user2->email = 'marwa@outlook.fr';
            $user2->password = Hash::make('siyou2020');
            $user2->save();
            // get role and assign it to a user
            $role2 = Role::where('name', 'Shop_Owner')->first();
            $role2->users()->save($user2);

            $user3 = new User();
            $user3->first_name = 'siyou';
            $user3->last_name = 'technology';
            $user3->email = 'siyou@outlook.fr';
            $user3->password = Hash::make('siyou2020');
            $user3->save();
            // get role and assign it to a user
            $role3 = Role::where('name', 'Super_Admin')->first();
            $role3->users()->save($user3);

            $user4 = new User();
            $user4->first_name = 'Sales';
            $user4->last_name = 'Manager';
            $user4->email = 'salesmanager@outlook.fr';
            $user4->password = Hash::make('siyou2020');
            $user4->save();
            // get role and assign it to a user
            $role4 = Role::where('name', 'SalesManager')->first();
            $role4->users()->save($user4);


            $user5 = new User();
            $user5->first_name = 'Safouen';
            $user5->last_name = 'missaoui';
            $user5->email = 'safouen@outlook.fr';
            $user5->password = Hash::make('siyou2019');
            $user5->save();
            // get role and assign it to a user
            $role5 = Role::where('name', 'SalesManager')->first();
            $role5->users()->save($user5);
        }
    }
}
