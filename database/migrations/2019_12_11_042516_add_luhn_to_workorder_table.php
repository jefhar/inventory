<?php

use Domain\WorkOrders\WorkOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLuhnToWorkorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            WorkOrder::TABLE,
            function (Blueprint $table) {
                $table->unsignedBigInteger(WorkOrder::LUHN)->nullable()->after(WorkOrder::USER_ID);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'workorder',
            function (Blueprint $table) {
                //
            }
        );
    }
}
