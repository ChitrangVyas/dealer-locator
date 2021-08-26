<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddColumnOrderReferenceToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_reference')->after('from_user')->nullable();
            $table->foreign('order_reference')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if(Schema::hasColumns('orders',['order_reference'])){
                $table->dropForeign(['order_reference']);
                $table->dropColumn(['order_reference']);
            }
        });
    }
}
