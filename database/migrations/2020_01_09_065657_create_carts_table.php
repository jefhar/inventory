<?php

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            Product::TABLE,
            function (Blueprint $table) {
                $table->unsignedBigInteger(Product::CART_ID)
                    ->nullable()
                    ->after(Product::WORK_ORDER_ID);
                $table->unsignedInteger(Product::PRICE)
                    ->default(0)
                    ->after(Product::LUHN);
            }
        );

        Schema::create(
            Cart::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(Cart::ID);
                $table->unsignedBigInteger(Cart::USER_ID)->nullable();
                $table->unsignedBigInteger(Cart::CLIENT_ID)->nullable();
                $table->unsignedBigInteger(Cart::LUHN)->nullable();
                $table->string(Cart::STATUS, 64)->default(Cart::STATUS_OPEN);
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists(Cart::TABLE);
    }
}
