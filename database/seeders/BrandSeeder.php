<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $brands = array([
           'id' => '1',
           'name' => 'Brand 1',
           'description' => 'Brand 1',
           'short_description' => 'Brand 1',
           'logo' => '/storage/uploads/brand.png',
           'manufacturer_id' => '1',
           'status' => '1',
           'created_at' => date('Y-m-d H:i:s'),
           'updated_at' => date('Y-m-d H:i:s'),
       ]);
       DB::table('brands')->insert($brands);
    }
}
