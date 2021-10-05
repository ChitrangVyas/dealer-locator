<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Null_;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array([
            'name' => "Administrator",
            'email' => "info@techcelerity.com",
            'password' => Hash::make('woodbird9'),
            'users_group_id'=>NULL,
            'parent_id'=>NULL,
            "address"=>"135-B, XNU Colony\r\nNear PQR station road",
            "state_id"=>"12",
            "city_id"=>"783",
            "zipcode"=>"380002",
            'email_verified_at' => NULL,
            'remember_token' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'name' => "Staff",
            'email' => "staff@mail.com",
            'password' => Hash::make('woodbird9'),
            'users_group_id'=>1,
            'parent_id'=>NULL,
            "address"=>"135-B, XNU Colony\r\nNear PQR station road",
            "state_id"=>"12",
            "city_id"=>"783",
            "zipcode"=>"380002",
            'email_verified_at' => NULL,
            'remember_token' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'name' => "Dealer",
            'email' => "dealer@mail.com",
            'password' => Hash::make('woodbird9'),
            'users_group_id'=>2,
            'parent_id'=>NULL,
            "address"=>"135-B, XNU Colony\r\nNear PQR station road",
            "state_id"=>"12",
            "city_id"=>"783",
            "zipcode"=>"380002",
            'email_verified_at' => NULL,
            'remember_token' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'name' => "Retailer",
            'email' => "retailer@mail.com",
            'password' => Hash::make('woodbird9'),
            'users_group_id'=>3,
            'parent_id'=>3,
            "address"=>"135-B, XNU Colony\r\nNear PQR station road",
            "state_id"=>"12",
            "city_id"=>"783",
            "zipcode"=>"380002",
            'email_verified_at' => NULL,
            'remember_token' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
    DB::table('users')->insert($users);
    }
}
