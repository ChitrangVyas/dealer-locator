<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['po','quote','sale']);
            $table->String('order_number',50);
            $table->Text('status');
            $table->ForeignId('to_user')->index();
            $table->ForeignId('from_user')->index();
            $table->ForeignId('product_id')->index()->constrained();
            $table->Integer('qty');
            $table->Decimal('amount',10,2);
            $table->timestamps();
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
        Schema::dropIfExists('orders');
    }
}
