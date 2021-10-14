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
        DB::table('tax_groups')->delete();
        $tax_group = array(
            array('id'=>1,'name'=>'No Tax','is_default'=>1,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')),
        );
        DB::table('tax_groups')->insert($tax_group);
    }
}
