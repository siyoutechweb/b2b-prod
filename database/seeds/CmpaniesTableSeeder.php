<?php


use App\Models\Company;
use Illuminate\Database\Seeder;

class CmpaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
        {
            $company = new Company();
            $company->name = 'DHL Express';
            $company->description = 'Courier service in Osmannero, Italy';
            $company->email = 'www.dhl.it/it/express.html';
            $company->phone = '+39 199 199 345';
            $company->adress = 'Via Ettore Maiorana, 63, 50019 Sesto Fiorentino FI, Italy';
            $company->save();
        }
    
}
