<?php

use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            Product::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(Product::ID);
                $table->unsignedBigInteger(Product::WORK_ORDER_ID);
                $table->unsignedBigInteger(Product::TYPE_ID)->nullable();
                $table->timestamps();
                $table->foreign(Product::WORK_ORDER_ID)->references(WorkOrder::ID)->on(WorkOrder::TABLE);
                $table->foreign(Product::TYPE_ID)->references(Type::ID)->on(Type::TABLE);
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
            Product::TABLE,
            function (Blueprint $table) {
                $table->dropForeign([Product::WORK_ORDER_ID]);
                $table->dropForeign([Product::TYPE_ID]);
            }
        );
        Schema::dropIfExists('products');
    }
}
