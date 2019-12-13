<?php

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
                $table->string(Type::NAME, 64)->unique()->nullable();
                $table->string(Type::SLUG, 64)->unique()->nullable();
                $table->json(Type::FORM)->nullable();
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
        Schema::dropIfExists(Type::TABLE);
    }
}
