<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountPercentageToOrderProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->double('discount_percentage',10,2)->nullable();
            $table->double('discount',10,2)->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_product', function (Blueprint $table) {
            //
        });
    }
}
