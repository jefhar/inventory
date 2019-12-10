<?php

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            Manufacturer::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(Manufacturer::ID);
                $table->string(Manufacturer::NAME)->unique();
                $table->timestamps();
            }
        );

        Schema::table(
            Product::TABLE,
            function (Blueprint $table) {
                $table->unsignedBigInteger(Product::MANUFACTURER_ID)->nullable()->after(Product::TYPE_ID);
                $table->foreign(Product::MANUFACTURER_ID)->references(Manufacturer::ID)->on(Manufacturer::TABLE);
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
            }
        );

        Schema::dropIfExists('manufacturers');
    }
}
