<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInquiriesLogTableRenameReminderToForCreatedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inquiries_log', function (Blueprint $table) {
            //
            $table->renameColumn('reminder', 'for_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries_log', function (Blueprint $table) {
            //
            $table->renameColumn('for_created_at', 'reminder');
        });
    }
}
