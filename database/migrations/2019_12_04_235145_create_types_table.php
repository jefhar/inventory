<?php

use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            Type::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(Type::ID);
                $table->timestamps();
            }
        );
        Schema::table(
            \Domain\Products\Models\Product::TABLE,
            function (Blueprint $table) {
                $table->unsignedBigInteger(Product::TYPE_ID)->nullable();
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
                $table->dropForeign([Product::TYPE_ID]);
            }
        );
        Schema::dropIfExists(Type::TABLE);
    }
}
