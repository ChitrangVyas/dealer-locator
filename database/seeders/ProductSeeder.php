<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = array(
            array('id' => '1','name' => 'Courier and Packing Charges','description' => 'Indstrial Application','image' => '/storage/uploads/products/product1.png','price' => '15000.00','brand_id' => '1','hsn_sac' => '996812','installation_place' => NULL,'max_pipe_size_and_water_flow' => NULL,'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL,'status' => '1','tax_group_id' => '3'),
            array('id' => '2','name' => 'Installation Charges','description' => 'Installation Charges','image' => '/storage/uploads/products/product2.png','price' => '2500.00','brand_id' => '1','hsn_sac' => '996813','installation_place' => NULL,'max_pipe_size_and_water_flow' => NULL,'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL,'status' => '1','tax_group_id' => '3'),
            array('id' => '3','name' => 'Water Softener Product 1','description' => 'Water Softener Product 1','image' => '/storage/uploads/products/product3.png','price' => '2500.00','brand_id' => '1','hsn_sac' => '996813','installation_place' => NULL,'max_pipe_size_and_water_flow' => NULL,'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL,'status' => '1','tax_group_id' => '3'),
            array('id' => '4','name' => 'Water Softener Product 2','description' => 'Water Softener Product 2','image' => '/storage/uploads/products/product4.png','price' => '2500.00','brand_id' => '1','hsn_sac' => '996813','installation_place' => NULL,'max_pipe_size_and_water_flow' => NULL,'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL,'status' => '1','tax_group_id' => '3'),
            );
          DB::table('products')->insert($products);
    }
}
