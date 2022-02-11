<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventory = array(
            array('id' => '1','qty' => '100','product_id' => '1','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '2','qty' => '100','product_id' => '2','user_id' => NULL,'low_stock' => '20','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '3','qty' => '100','product_id' => '3','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '4','qty' => '100','product_id' => '4','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '5','qty' => '100','product_id' => '5','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '6','qty' => '100','product_id' => '6','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '7','qty' => '100','product_id' => '7','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '8','qty' => '100','product_id' => '8','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '9','qty' => '100','product_id' => '9','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '10','qty' => '100','product_id' => '10','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '11','qty' => '100','product_id' => '11','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '12','qty' => '100','product_id' => '12','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '13','qty' => '100','product_id' => '13','user_id' => NULL,'low_stock' => NULL,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '14','qty' => '100','product_id' => '14','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '15','qty' => '100','product_id' => '15','user_id' => NULL,'low_stock' => '20','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '16','qty' => '100','product_id' => '16','user_id' => NULL,'low_stock' => '50','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '17','qty' => '100','product_id' => '17','user_id' => NULL,'low_stock' => '50','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '18','qty' => '100','product_id' => '18','user_id' => NULL,'low_stock' => '10','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'))           
          );
          DB::table('inventory')->insert($inventory);
    }
}
