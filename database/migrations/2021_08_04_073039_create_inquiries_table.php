<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiries_source_id')->index();
            $table->foreignId('product_id')->index()->constrained();
            $table->foreignId('customer_id')->index();
            $table->foreignId('dealer_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
            $table->foreign('inquiries_source_id')->references('id')->on('inquiries_source');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('dealer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
}
