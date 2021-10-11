<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxGroupItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_group_id')->nullable()->constrained();
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->float('percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_group_items');
    }
}
