<?php

use Illuminate\Database\Seeder;
use App\Models\CriteriaBase;
use App\Models\CriteriaUnit;

class CriteriaBaseTabeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $criteria_base = new criteriaBase();
       $criteria_base->name="weight";
       $criteria_base->save();
       $criteria_unit = new criteriaUnit();
       $criteria_unit->unit_name="pounds";
       $criteria_unit->criteria_base_id= $criteria_base->id;
       $criteria_unit->save();
       $criteria_unit1 = new criteriaUnit();
       $criteria_unit1->unit_name="kg";
       $criteria_unit1->criteria_base_id= $criteria_base->id;
       $criteria_unit1->save();

       $criteria_base1 = new criteriaBase();
       $criteria_base1->name="color";
       $criteria_base1->save();
       $criteria_unit2 = new criteriaUnit();
       $criteria_unit2->unit_name="hex";
       $criteria_unit2->criteria_base_id= $criteria_base1->id;
       $criteria_unit2->save();
       $criteria_unit3 = new criteriaUnit();
       $criteria_unit3->unit_name="rgb";
       $criteria_unit3->criteria_base_id= $criteria_base1->id;
       $criteria_unit3->save();

    }
}
