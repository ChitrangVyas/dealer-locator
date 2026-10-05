<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create(array('id' => '1','slug' => 'settings','json_data' => '{"site_name":"Locate Symphony Limited","name":"Locate Symphony Limited","email":"info@hvinfotech.com","mobile":"9874563210","address":"HV Infotech, C 307, Ganesh Marridian, Sola.","country":"101","state":"12","city":"783","zipcode":"380060","gstin":null,"google_map_api_key":null,"google_map_radius":"20"}','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')));
    }
}
