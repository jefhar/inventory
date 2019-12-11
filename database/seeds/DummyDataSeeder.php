<?php

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

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
        /** @var Collection[Type] $types */
        $types = collect();
        for ($i = 0; $i < 6; $i++) {
            $type = new Type();
            $type->name = $faker->unique()->word;
            $type->save();
            $types->push($type);
        }
        // Make 1 type with a sluggable name.
        $type = new Type();
        $type->name = $faker->words(2, true);
        $type->save();
        $types->push($type);

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
            $product = factory(Product::class)->make();
            $type = $types->random();
            $product->type()->associate($type);
            $workOrder->products()->save($product);
        }
    }
}
