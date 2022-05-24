<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventory = array(
            array('id' => '1','inquiries_source_id' => '4','customer_id' => '5','dealer_id' => '3','retailer_id' => '4','created_by' => '1','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL,'comments' => 'Purchase of Water Softener Product 1','application' => 'Industrial','application_type' => 'Common Utility','pump_capacity' => NULL,'pressure_pump' => NULL,'submersible_pump' => NULL,'water_flow' => NULL,'other_gravity' => NULL,'pipe_diameters' => NULL,'tds' => NULL,'hardness' => NULL,'bore_well_water' => NULL,'soft_water' => NULL,'others_water_type' => NULL)
        );
          DB::table('inquiries')->insert($inventory);
    }
}
