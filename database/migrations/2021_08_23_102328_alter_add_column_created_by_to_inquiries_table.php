<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnCreatedByToInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('created_by')->after('dealer_id')->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users');
            $table->boolean('status')->after('created_by')->nullable()->default(0);
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
            if(Schema::hasColumns('inquiries',['created_by','status'])){
                $table->dropForeign(['created_by']);
                $table->dropColumn(['created_by']);
                $table->dropColumn(['status']);
            }
        });
    }
}
