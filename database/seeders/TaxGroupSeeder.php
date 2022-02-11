<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tax_group_items')->delete();
        DB::table('tax_groups')->delete();
        DB::table('taxes')->delete();
        $taxes = array(
            array('id'=>1,'name'=>'CGST','slug'=>'cgst','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>2,'name'=>'SGST','slug'=>'sgst','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>3,'name'=>'IGST','slug'=>'igst','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'))            
        );
        DB::table('taxes')->insert($taxes);

        $tax_group = array(
            array('id'=>1,'name'=>'Zero Tax','is_default'=>0,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>2,'name'=>'GST 10%','is_default'=>1,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>3,'name'=>'GST 18%','is_default'=>0,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
        );
        DB::table('tax_groups')->insert($tax_group);
        
        $tax_group_items = array(
            array('id'=>1,'tax_group_id'=>'1','tax_id'=>1, 'percentage'=>"0.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>2,'tax_group_id'=>'1','tax_id'=>2, 'percentage'=>"0.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>3,'tax_group_id'=>'1','tax_id'=>3, 'percentage'=>"0.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>4,'tax_group_id'=>'2','tax_id'=>1, 'percentage'=>"5.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>5,'tax_group_id'=>'2','tax_id'=>2, 'percentage'=>"5.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>6,'tax_group_id'=>'2','tax_id'=>3, 'percentage'=>"10.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>7,'tax_group_id'=>'1','tax_id'=>1, 'percentage'=>"9.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>8,'tax_group_id'=>'1','tax_id'=>2, 'percentage'=>"9.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>9,'tax_group_id'=>'1','tax_id'=>3, 'percentage'=>"18.00",'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            
        );
        DB::table('tax_group_items')->insert($tax_group_items);
    }
}
