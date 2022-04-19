<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users_group')->delete();
        $user_group = array(
            array('id'=>1,'name'=>'Staff','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>2,'name'=>'Dealer','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>3,'name'=>'Retailer','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>4,'name'=>'Customer','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>5,'name'=>'Vendor','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
        );
        DB::table('users_group')->insert($user_group);
    }
}
