<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = array([
            'id' => '1',
            'name' => 'Industrial Application',
            'logo' => '/storage/uploads/1632989589_1632989283_logo2.png',
            'description' => '<p></p><p></p><p><span style="color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 14px; text-align: center;">Anti-scale system for industrial arena are designed to treat pipe diameters up to 30" and can be used for various applications in medium and heavy industries.</span></p><ul><li><span style="font-size: 1rem;">Boilers</span></li><li>Washing</li><li>Air Compressor</li><li>Gasifier System</li><li>Heat Exchanger</li><li>Plastic Moulding</li><li>Captive Power Plant</li><li>Induction Furnace</li><li>Jacket Cooling</li></ul>',         
            'parent_category' => '0',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'id' => '2',
            'name' => 'Commercial Application',
            'logo' => '/storage/uploads/1632989589_1632989283_logo2.png',
            'description' => '<p>The commercial units are designed to treat pipe diameters up to 4" for small to large commercial buildings and complexes. The predefined programs can be adjusted to the needs of the applications as well as the type of pipe material and diameter.</p><ul><li>Piping Systems</li><li>Water Heaters</li><li>Showers and Faucets</li><li>Swimming Pools</li></ul><p></p>',         
            'parent_category' => '0',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'id' => '3',
            'name' => 'Residential Application',
            'logo' => '/storage/uploads/1632989589_1632989283_logo2.png',
            'description' => '<p>The Anti-scale system can also be used to protect the piping system in individual houses, farmhouses, or weekend homes.</p><ul><li><span style="font-size: 1rem;">Piping System</span></li><li>Solar Heater</li><li>Washing Machine</li><li>Pools and Jacuzzis</li><li>Rain Showers and Faucets</li></ul>',         
            'parent_category' => '0',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'id' => '4',
            'name' => 'Agriculture Application',
            'logo' => '/storage/uploads/1632989589_1632989283_logo2.png',
            'description' => '<p>The Anti-scaling system can be used in agriculture where the water is used from underground bore well. It keeps the pipes free from the scale deposits; drip irrigation fine nozzles remain clean; helps in maintaining the natural mineral-rich composition of the water useful for agriculture.</p><ul><li><span style="font-size: 1rem;">Dripper and Sprinkles</span></li><li>Drip Irrigation</li><li>Sprinkler System</li></ul>',         
            'parent_category' => '0',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
        DB::table('categories')->insert($category);
    }
}
