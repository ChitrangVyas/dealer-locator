<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterManufacturersTableAddColumnsToManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->string('mobile',15)->after('email')->nullable();
            $table->Text('address')->after('logo')->nullable();
            $table->unsignedInteger('city_id')->after('address')->nullable();
            $table->unsignedInteger('state_id')->after('city_id')->nullable();
            $table->Text('zipcode')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');           
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manufacturers', function (Blueprint $table) {
            //
            if(Schema::hasColumns('manufacturers',['mobile','address','city_id', 'state_id', 'zipcode'])){
                $table->dropForeign(['city_id']);
                $table->dropForeign(['state_id']);
                Schema::dropColumns('manufacturers',['mobile','address','city_id', 'state_id', 'zipcode']);
            }
        });
    }
}
