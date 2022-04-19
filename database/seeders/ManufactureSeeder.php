<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ManufactureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manufacturer = array([
            'name' => 'Manufacturer 1',
            'email' => 'manufacturer1@gmail.com',
            'mobile' => '9824655419',
            'logo' => '/storage/uploads/manufacturer.png',
            'country_id' => '101',
            'state_id' => '12',
            'city_id' => '783',
            'zipcode' => '380060',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        DB::table('manufacturers')->insert($manufacturer);
    }
}
