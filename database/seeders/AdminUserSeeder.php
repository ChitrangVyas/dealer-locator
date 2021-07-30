<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => "Mehul",
            'email' => "admin@admin.com",
            'password' => '$2y$10$qNgt.O4kXnGVrRThkRa4COLxsaENfO/gCRUpv1Pe7l2A1X9CRq8K2',
            'email_verified_at' => "",
            'remember_token' => "",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
