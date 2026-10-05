<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('countries')->delete();
        $countries = array(
            array('id' => 1,'code' => 'IN','name' => "India",'phonecode' => '+91'),
            );
            DB::table('countries')->insert($countries);
    }
}
