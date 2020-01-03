<?php

use Domain\Products\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductTable extends Migration
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
                $table->unsignedBigInteger(Product::LUHN)
                    ->nullable()
                    ->after(Product::WORK_ORDER_ID);
                $table->string(Product::STATUS, 64)
                    ->default(Product::STATUS_AVAILABLE)
                    ->after(Product::MODEL);
                $table->string(Product::SERIAL, 64)
                    ->nullable()
                    ->after(Product::MODEL);
                $table->index(Product::SERIAL);
            }
        );

        $products = Product::all();
        foreach ($products as $product) {
            $product->luhn = \Tdely\Luhn\Luhn::create($product->id);
            $product->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'products',
            function (Blueprint $table) {
            }
        );
    }
}
