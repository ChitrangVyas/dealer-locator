<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddColumnTaxToOrderTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_tax', function (Blueprint $table) {
            $table->decimal('tax',10,2)->after('order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_tax', function (Blueprint $table) {
            if(Schema::hasColumn('order_tax','tax')){
                $table->dropColumn('tax');
            }
        });
    }
}
