<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InquirySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '1','source' => 'Google Search','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '2','source' => 'Facebook Paid Marketing','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '3','source' => 'Instagram Paid Marketing','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '4','source' => 'Google Paid Marketing','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s') ,'deleted_at' => NULL),
            array('id' => '5','source' => 'Reference of Existing Customer','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '6','source' => 'Cold Calling','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '7','source' => 'Email Marketing','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL),
            array('id' => '8','source' => 'SMS Marketing','status' => '1','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),'deleted_at' => NULL)
          );
        
          DB::table('inquiries_source')->insert($data);
    }
}
