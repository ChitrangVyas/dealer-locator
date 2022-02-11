<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_category = array(
            array('id' => '1','product_id' => '1','category_id' => '2','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '2','product_id' => '2','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '3','product_id' => '3','category_id' => '2','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '4','product_id' => '4','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '5','product_id' => '5','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '6','product_id' => '6','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '7','product_id' => '7','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '8','product_id' => '8','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '9','product_id' => '9','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '10','product_id' => '10','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '11','product_id' => '11','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '12','product_id' => '12','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '13','product_id' => '13','category_id' => '3','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '14','product_id' => '15','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '15','product_id' => '16','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '16','product_id' => '17','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '17','product_id' => '18','category_id' => '1','created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'))
          );
          DB::table('product_category')->insert($product_category);
    }
}
