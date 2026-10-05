<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDealerTypeToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'dealer_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('dealer_type', 50)->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'dealer_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dealer_type');
            });
        }
    }
}
