<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->String('mobile',15)->nullable();
            $table->foreignId('users_group_id')->nullable()->index();
            $table->bigInteger('parent_id')->unsigned()->nullable()->index();
            $table->bigInteger('created_by')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by')->unsigned()->nullable()->index();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['users_group_id', '','city_id', 'state_id', 'zipcode', 'parent_id','created_by','updated_by']);
        });
    }
}
