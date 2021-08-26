<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddColumnsIsRecievedAndRecievedDateToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_po_recieved')->after('total')->nullable()->default(0);
            $table->dateTime('recieved_date')->after('is_po_recieved')->nullable();
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
            if(Schema::hasColumns('orders',['is_po_recieved','recieved_date'])){
                $table->dropColumn(['is_po_recieved']);
                $table->dropColumn(['recieved_date']);
            }
        });
    }
}
