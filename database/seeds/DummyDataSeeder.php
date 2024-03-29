<?php

use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
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
        $faker = Faker\Factory::create();
        /** @var Client $client */

        $models = collect();
        for ($i = 0; $i < 6; $i++) {
            $model = $faker->jobTitle;
            $models->push($model);
        }
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
            $workOrder->intake = $faker->text();
            $workOrder->user_id = 1;
            $client->workOrders()->save($workOrder);
            $product = factory(Product::class)->make(
                [
                    Product::MODEL => $models->random(),
                    Product::VALUES => ['serial' => $faker->isbn10],
                ]
            );
            $workOrder->products()->save($product);
        }
    }
}
