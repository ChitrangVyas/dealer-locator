<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMSSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json_data = array(
            "api_key"=>"",
            "sender_id"=>"",
            "sms_type"=>"",
            "entity_id"=>"",
            "template_id"=>""
        );

        $sms_settings = array(
            'slug'=>'sms-settings',
            'json_data'=>json_encode($json_data),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        DB::table('settings')->where('slug','sms-settings')->delete();
        DB::table('settings')->insert($sms_settings);
        DB::table('settings')->where('id','1')->update(['slug'=>'settings']);
    }
}
