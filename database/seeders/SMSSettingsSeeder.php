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
       Settings::create(array('id' => '2','slug' => 'sms-settings','json_data' => '{"is_enabled":"0","userId":"","sms_password":"","sender_id":"","sms_type":"2","entity_id":"","template_id":{"inquiry_assign":"","inquiry_customer_assigned":""}}','created_at' => '2022-02-25 11:26:34','updated_at' => '2022-04-07 13:19:52'));
    }
}
