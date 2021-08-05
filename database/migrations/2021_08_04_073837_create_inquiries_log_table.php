<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiriesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries_log', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['email','calls','meetings','notes']);
            $table->DateTime('reminder');
            $table->Text('description');
            $table->ForeignId('inquiry_id')->index()->constrained();
            $table->ForeignId('logged_by')->index();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
            $table->foreign('logged_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries_log');
    }
}
