<?php

use Domain\Products\Models\Product;
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
        Schema::dropIfExists(Product::TABLE);
    }
}
