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
        Settings::create(array('id' => '1','slug' => 'settings','json_data' => '{"site_name":"Dealer CRM by Tech Celerity GLobal Solutions Pvt. Ltd.","timezone":"Asia\\/Kolkata","name":"Dealer CRM by Tech Celerity GLobal Solutions Pvt. Ltd.","email":"info@techcelerity.com","mobile":"9714033266","address":"TechCelerity Global Solution PVT LTD, C 307, Ganesh Marridian, Sola.","country":"101","state":"12","city":"783","zipcode":"380060","gstin":null,"allowed_countries":["101","153","231","230","202","165","229","113"],"terms_condition_sale":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.","terms_condition_po":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.","terms_condition_quote":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.","mail_mailer":"sendmail","mail_username":"3e15370474bb65","mail_password":"woodbird9","mail_host":"smtp.mailtrap.io","mail_port":"2525","mail_encryption":"tls","mail_from_email":"info@techcelerity.com","mail_from_name":"Tech Celerity GLobal Solutions Pvt. Ltd.","logo":"\\/storage\\/uploads\\/logo\\/1645773788_1645772724_background.png","icon_logo":"\\/storage\\/uploads\\/logo\\/1645772544_t_c-favicon.png"}','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')));
    }
}
