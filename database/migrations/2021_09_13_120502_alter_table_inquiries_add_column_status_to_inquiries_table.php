<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInquiriesAddColumnStatusToInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            //
            if(Schema::hasColumns('inquiries',['status'])){
                $table->integer('status')->nullable()->change();
            }else{
                $table->integer('status')->nullable()->default(1)->after('created_by');
            }
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
            if(Schema::hasColumns('inquiries',['status'])){
                $table->dropColumn(['status']);
            }
        });
    }
}
