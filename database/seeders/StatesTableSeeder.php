<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('states')->delete();
		$states = array(
			array('name' => "Andaman and Nicobar Islands",'country_id' => 1),
			array('name' => "Andhra Pradesh",'country_id' => 1),
			array('name' => "Arunachal Pradesh",'country_id' => 1),
			array('name' => "Assam",'country_id' => 1),
			array('name' => "Bihar",'country_id' => 1),
			array('name' => "Chandigarh",'country_id' => 1),
			array('name' => "Chhattisgarh",'country_id' => 1),
			array('name' => "Dadra and Nagar Haveli",'country_id' => 1),
			array('name' => "Daman and Diu",'country_id' => 1),
			array('name' => "Delhi",'country_id' => 1),
			array('name' => "Goa",'country_id' => 1),
			array('name' => "Gujarat",'country_id' => 1),
			array('name' => "Haryana",'country_id' => 1),
			array('name' => "Himachal Pradesh",'country_id' => 1),
			array('name' => "Jammu and Kashmir",'country_id' => 1),
			array('name' => "Jharkhand",'country_id' => 1),
			array('name' => "Karnataka",'country_id' => 1),
			array('name' => "Kenmore",'country_id' => 1),
			array('name' => "Kerala",'country_id' => 1),
			array('name' => "Lakshadweep",'country_id' => 1),
			array('name' => "Madhya Pradesh",'country_id' => 1),
			array('name' => "Maharashtra",'country_id' => 1),
			array('name' => "Manipur",'country_id' => 1),
			array('name' => "Meghalaya",'country_id' => 1),
			array('name' => "Mizoram",'country_id' => 1),
			array('name' => "Nagaland",'country_id' => 1),
			array('name' => "Narora",'country_id' => 1),
			array('name' => "Natwar",'country_id' => 1),
			array('name' => "Odisha",'country_id' => 1),
			array('name' => "Paschim Medinipur",'country_id' => 1),
			array('name' => "Pondicherry",'country_id' => 1),
			array('name' => "Punjab",'country_id' => 1),
			array('name' => "Rajasthan",'country_id' => 1),
			array('name' => "Sikkim",'country_id' => 1),
			array('name' => "Tamil Nadu",'country_id' => 1),
			array('name' => "Telangana",'country_id' => 1),
			array('name' => "Tripura",'country_id' => 1),
			array('name' => "Uttar Pradesh",'country_id' => 1),
			array('name' => "Uttarakhand",'country_id' => 1),
			array('name' => "Vaishali",'country_id' => 1),
			array('name' => "West Bengal",'country_id' => 1),
		);
		DB::table('states')->insert($states);
	}
}
