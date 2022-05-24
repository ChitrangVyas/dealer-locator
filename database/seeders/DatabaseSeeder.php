<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([          
            CountriesTableSeeder::class,            
            StatesTableSeeder::class,
            CitiesTableSeeder::class, 
            UserGroupSeeder::class,
            SettingsSeeder::class,
            SMSSettingsSeeder::class,
            AdminUserSeeder::class,
            TaxGroupSeeder::class,
            ManufactureSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductCategorySeeder::class,
            InventorySeeder::class,
            InquirySourceSeeder::class,
            InquirySeeder::class,
            currencyTableSeeder::class 
        ]);
    }
}
