<?php

use App\User;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            WorkOrder::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(WorkOrder::ID);
                $table->unsignedBigInteger(WorkOrder::CLIENT_ID);
                $table->unsignedBigInteger(WorkOrder::USER_ID);
                $table->boolean(WorkOrder::IS_LOCKED)->default(false);
                $table->string(WorkOrder::INTAKE);
                $table->timestamps();
                $table->foreign(WorkOrder::CLIENT_ID)->references(Client::ID)->on(Client::TABLE);
                $table->foreign(WorkOrder::USER_ID)->references(User::ID)->on(User::TABLE);
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
            WorkOrder::TABLE,
            function (Blueprint $table) {
                $table->dropForeign([WorkOrder::CLIENT_ID]);
                $table->dropForeign([WorkOrder::USER_ID]);
            }
        );
        Schema::dropIfExists(WorkOrder::TABLE);
    }
}
