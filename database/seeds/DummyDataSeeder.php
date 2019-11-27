<?php

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
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
        /** @var Client $client */
        for ($i = 0; $i < 20; $i++) {
            $client = factory(Client::class)->create();
            $person = factory(Person::class)->make();
            $client->person()->save($person);
        }
        for ($i = 0; $i < 200; $i++) {
            $client = Client::inRandomOrder()->first();
            $workOrder = new WorkOrder();

            /** @noinspection RandomApiMigrationInspection */
            $workOrder->is_locked = (bool)rand(0, 1);
            $workOrder->user_id = 1;
            $client->workOrders()->save($workOrder);
        }
    }
}
