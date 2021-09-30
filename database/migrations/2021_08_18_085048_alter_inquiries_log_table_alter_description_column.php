<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInquiriesLogTableAlterDescriptionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('inquiries_log',function($table){
            $table->dateTime('reminder')->nullable()->change();
            $table->string('source')->nullable();
            $table->boolean('is_reminder')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries_log',function($table){
            $table->dateTime('reminder')->nullable(false)->change();
            $table->dropColumn('is_reminder');
            $table->dropColumn('source');
        });
    }
}
