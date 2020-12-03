<?php

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') == 'production') {
            $path = 'database/seeds/SQLFiles/Companies.sql';
            DB::unprepared(file_get_contents($path));
            $this->command->info('Companies table seeded!');
        } else {

            // DHL
            $company = new Company();
            $company->name = 'DHL Express';
            $company->description = 'Courier service in Osmannero, Italy';
            $company->email = 'www.dhl.it/it/express.html';
            $company->phone = '+39 199 199 345';
            $company->image_url = 'https://www.ecommerce-nation.fr/wp-content/uploads/2019/10/dhl-express-transporteur-international-livraison-rapide-analyse-avis-1.png';
            $company->adress = 'Via Ettore Maiorana, 63, 50019 Sesto Fiorentino FI, Italy';
            $company->save();

            // UPS
            $company2 = new Company();
            $company2->name = 'UPS';
            $company2->description = 'UPS';
            $company2->email = 'www.dhl.it/it/express.html';
            $company2->phone = '+39 404 828 6000';
            $company2->image_url = 'https://www.ups.com/assets/resources/images/UPS_logo.svg';
            $company2->adress = 'Via Ettore Maiorana, 63, 50019 Sesto Fiorentino FI, Italy';
            $company2->save();
        }
    }
}
