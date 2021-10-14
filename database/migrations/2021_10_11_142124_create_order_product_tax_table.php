<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_tax', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->nullable();
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->double('price',10,2)->nullable();
            $table->timestamps();
        });
        Schema::table('order_product_tax', function (Blueprint $table) {
            $table->foreign('order_product_id')->references('id')->on('order_product')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_tax');
    }
}
