<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderTableDropProductQtyAmountColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumns('orders', ['product_id','qty','amount'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id','qty','amount']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
