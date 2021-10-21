<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaxColumnToOrderProductTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_tax', function (Blueprint $table) {
            //
            $table->dropForeign(['tax_id']);
            $table->foreign('tax_id')
            ->references('id')->on('taxes')
            ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_product_tax', function (Blueprint $table) {
            //
        });
    }
}
