<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
