<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Tests\TestCase;

/**
 * Class PersonClassTest
 *
 * @package Tests\Unit
 */
class PersonClassTest extends TestCase
{
    /**
     * @test
     */
    public function phoneNumberFormatsAndUnFormats(): void
    {
        $faker = factory(Person::class)->make();

        $phone = Person::unformatPhoneNumber($faker->phone_number);
        $this->assertRegExp("/^\d+$/", $phone);

        $phone = Person::unformatPhoneNumber('(510) 555-0123');
        $this->assertEquals('5105550123', $phone);
        $this->assertEquals('(510) 555-0123', Person::formatPhoneNumber($phone));

        $phone = Person::unformatPhoneNumber('(510) 555-0123  x');
        $this->assertEquals('5105550123', $phone);
        $this->assertEquals('(510) 555-0123', Person::formatPhoneNumber($phone));

        $phone = Person::unformatPhoneNumber('(510) 555-0123  x82374 ');
        $this->assertEquals('510555012382374', $phone);
        $this->assertEquals('(510) 555-0123 x82374', Person::formatPhoneNumber($phone));

        $phone = Person::formatPhoneNumber('');
        $this->assertEquals('(000) 000-0000', $phone);
        $phone = Person::unformatPhoneNumber($phone);
        $this->assertEquals('0000000000', $phone);

        $client = new Client();
        $client->save();

        $person = new Person();

        $person->phone_number = '000-000-0000';
        $client->person()->save($person);
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::PHONE_NUMBER => '0000000000',
            ]
        );
    }
}
