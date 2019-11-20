<?php

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            Person::TABLE,
            function (Blueprint $table) {
                $table->bigIncrements(Person::ID);
                $table->unsignedBigInteger(Person::CLIENT_ID);
                $table->string(Person::EMAIL);
                $table->string(Person::FIRST_NAME);
                $table->string(Person::LAST_NAME);
                $table->string(Person::PHONE_NUMBER);
                $table->timestamps();

                $table->foreign(Person::CLIENT_ID)->references(Client::ID)->on(Client::TABLE);
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
            Person::TABLE,
            function (Blueprint $table) {
                $table->dropForeign([Person::CLIENT_ID]);
            }
        );
        Schema::dropIfExists(Person::TABLE);
    }
}
