<?php

namespace Database\Seeders;

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
            'name' => 'Kashyap Technology',
            'email' => 'kashyap@kashyap.com',
            'mobile' => '9898989898',
            'logo' => '/storage/uploads/1632989406_logo2.png',
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
