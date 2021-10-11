<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddColumnTaxGroupIdToPoductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tax_group_id')->nullable()->constrained();
            // $table->foreign('tax_group_id')->references('id')->on('tax_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if(Schema::hasColumns('products',['tax_group_id'])){
                $table->dropForeign(['tax_group_id']);
                $table->dropColumn(['tax_group_id']);
            }
        });
    }
}
