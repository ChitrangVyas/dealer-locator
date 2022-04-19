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
        $taxes = array(
            array('id' => '1','name' => 'CGST','slug' => 'cgst','status' => '1','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-02-25 11:26:34'),
            array('id' => '2','name' => 'SGST','slug' => 'sgst','status' => '1','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-02-25 11:26:34'),
            array('id' => '3','name' => 'IGST','slug' => 'igst','status' => '1','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-02-25 11:26:34')
        );
        DB::table('taxes')->insert($taxes);

        $tax_group = array(
            array('id' => '1','name' => 'Zero Tax','status' => '1','is_default' => '0','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '2','name' => 'GST 5%','status' => '1','is_default' => '1','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '3','name' => 'GST 18%','status' => '1','is_default' => '0','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '4','name' => 'GST 12%','status' => '1','is_default' => '0','created_at' => '2022-04-19 13:46:43','updated_at' => '2022-04-19 13:46:43'),
            array('id' => '5','name' => 'GST 28%','status' => '1','is_default' => '0','created_at' => '2022-04-19 13:46:57','updated_at' => '2022-04-19 13:46:57')
          );
        DB::table('tax_groups')->insert($tax_group);
        
        $tax_group_items = array(
            array('id' => '19','tax_group_id' => '2','tax_id' => '1','percentage' => '2.50','created_at' => '2022-04-19 13:46:21','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '20','tax_group_id' => '2','tax_id' => '2','percentage' => '2.50','created_at' => '2022-04-19 13:46:21','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '21','tax_group_id' => '2','tax_id' => '3','percentage' => '5.00','created_at' => '2022-04-19 13:46:21','updated_at' => '2022-04-19 13:46:21'),
            array('id' => '22','tax_group_id' => '3','tax_id' => '1','percentage' => '9.00','created_at' => '2022-04-19 13:46:31','updated_at' => '2022-04-19 13:46:31'),
            array('id' => '23','tax_group_id' => '3','tax_id' => '2','percentage' => '9.00','created_at' => '2022-04-19 13:46:31','updated_at' => '2022-04-19 13:46:31'),
            array('id' => '24','tax_group_id' => '3','tax_id' => '3','percentage' => '18.00','created_at' => '2022-04-19 13:46:31','updated_at' => '2022-04-19 13:46:31'),
            array('id' => '25','tax_group_id' => '4','tax_id' => '1','percentage' => '6.00','created_at' => '2022-04-19 13:46:43','updated_at' => '2022-04-19 13:46:43'),
            array('id' => '26','tax_group_id' => '4','tax_id' => '2','percentage' => '6.00','created_at' => '2022-04-19 13:46:43','updated_at' => '2022-04-19 13:46:43'),
            array('id' => '27','tax_group_id' => '4','tax_id' => '3','percentage' => '12.00','created_at' => '2022-04-19 13:46:43','updated_at' => '2022-04-19 13:46:43'),
            array('id' => '28','tax_group_id' => '5','tax_id' => '1','percentage' => '14.00','created_at' => '2022-04-19 13:46:57','updated_at' => '2022-04-19 13:46:57'),
            array('id' => '29','tax_group_id' => '5','tax_id' => '2','percentage' => '14.00','created_at' => '2022-04-19 13:46:57','updated_at' => '2022-04-19 13:46:57'),
            array('id' => '30','tax_group_id' => '5','tax_id' => '3','percentage' => '28.00','created_at' => '2022-04-19 13:46:57','updated_at' => '2022-04-19 13:46:57'),
            array('id' => '31','tax_group_id' => '1','tax_id' => '1','percentage' => '0.00','created_at' => '2022-04-19 13:47:05','updated_at' => '2022-04-19 13:47:05'),
            array('id' => '32','tax_group_id' => '1','tax_id' => '2','percentage' => '0.00','created_at' => '2022-04-19 13:47:05','updated_at' => '2022-04-19 13:47:05'),
            array('id' => '33','tax_group_id' => '1','tax_id' => '3','percentage' => '0.00','created_at' => '2022-04-19 13:47:05','updated_at' => '2022-04-19 13:47:05')
          );
        DB::table('tax_group_items')->insert($tax_group_items);
    }
}
