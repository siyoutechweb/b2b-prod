<?php

use App\Models\Statut;
use Illuminate\Database\Seeder;

class StatutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statut = new Statut();
        $statut->statut_name = 'in basket';
        $statut->save();
        $statut1 = new Statut();
        $statut1->statut_name = 'validated by shop owner';
        $statut1->save();
        $statut2 = new Statut();
        $statut2->statut_name = 'waiting for supplier validation';
        $statut2->save();
        $statut3 = new Statut();
        $statut3->statut_name = 'validated by supplier';
        $statut3->save();
        $statut4 = new Statut();
        $statut4->statut_name = 'paid';
        $statut4->save();
    }
}
