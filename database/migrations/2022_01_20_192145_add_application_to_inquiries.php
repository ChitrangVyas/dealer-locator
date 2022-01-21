<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationToInquiries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('application')->nullable();
            $table->string('application_type')->nullable();
            $table->string('pump_capacity')->nullable();
            $table->string('pressure_pump')->nullable();
            $table->string('submersible_pump')->nullable();
            $table->string('water_flow')->nullable();
            $table->string('other_gravity')->nullable();
            $table->string('pipe_diameters')->nullable();
            $table->string('tds')->nullable();
            $table->string('hardness')->nullable();
            $table->string('bore_well_water')->nullable();
            $table->string('soft_water')->nullable();
            $table->string('others_water_type')->nullable();        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            //
        });
    }
}
