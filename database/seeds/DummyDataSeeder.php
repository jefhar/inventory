<?php

use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            $client = factory(\Domain\WorkOrders\Client::class)->create();
            $person = factory(\Domain\WorkOrders\Person::class)->make();
            $client->person()->save($person);
        }
    }
}
