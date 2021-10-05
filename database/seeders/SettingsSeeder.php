<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'json_data'=>'{"site_name":"Techcelerity","timezone":"Asia\/Kolkata","name":"Techcelerity","email":"techcelerity@mail.com","mobile":"1234567890","address":"135-B, XNU Colony\r\nNear PQR station road","country":"101","state":"12","city":"783","zipcode":"380002","mail_mailer":"smtp","mail_username":"3e15370474bb65","mail_password":"3bf27f5520e7ae","mail_host":"smtp.mailtrap.io","mail_port":"2525","mail_encryption":"tls","mail_from_email":"info@techcelerity.com","mail_from_name":"Techcelerity","logo":"","icon_logo":""}',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
