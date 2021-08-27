<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddColumnRetailerIdToInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('retailer_id')->after('dealer_id')->nullable();
            $table->foreign('retailer_id')->references('id')->on('users');
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
            if(Schema::hasColumns('inquiries',['retailer_id'])){
                $table->dropForeign(['retailer_id']);
                $table->dropColumn(['retailer_id']);
            }
        });
    }
}
