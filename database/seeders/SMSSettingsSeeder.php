<?php

namespace Database\Seeders;
use App\Models\Settings;
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
       Settings::create(array('id' => '2','slug' => 'sms-settings','json_data' => '{"is_enabled":"1","userId":"EWTIND","sms_password":"12345","sender_id":"EWTIND","sms_type":"2","entity_id":"1701159280816643292","template_id":{"inquiry_assign":"1707164153532138101","inquiry_customer_assigned":"1707164923817866741"}}','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-04-07 13:19:52'));
    }
}
