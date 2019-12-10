<?php

use Domain\Products\Models\Manufacturer;
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
                $table->unsignedBigInteger(Product::MANUFACTURER_ID);
                $table->unsignedBigInteger(Product::TYPE_ID);
                $table->unsignedBigInteger(Product::WORK_ORDER_ID);
                $table->string(Product::MODEL)->nullable();
                $table->json(Product::VALUES)->nullable();

                $table->timestamps();

                $table->foreign(Product::MANUFACTURER_ID)->references(Manufacturer::ID)->on(Manufacturer::TABLE);
                $table->foreign(Product::TYPE_ID)->references(Type::ID)->on(Type::TABLE);
                $table->foreign(Product::WORK_ORDER_ID)->references(WorkOrder::ID)->on(WorkOrder::TABLE);
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
                $table->dropForeign([Product::MANUFACTURER_ID]);
                $table->dropForeign([Product::TYPE_ID]);
                $table->dropForeign([Product::WORK_ORDER_ID]);
            }
        );
        Schema::dropIfExists(Product::TABLE);
    }
}
