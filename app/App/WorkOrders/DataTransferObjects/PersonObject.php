<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Domain\WorkOrders\Person;
use Spatie\DataTransferObject\DataTransferObject;

class PersonObject extends DataTransferObject
{
    public string $email;
    public string $first_name;
    public string $last_name;
    public string $phone_number;

    /**
     * @param array $validated
     * @return static
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            [
                Person::EMAIL => $validated[Person::EMAIL] ?? Person::DEFAULT_EMAIL,
                Person::FIRST_NAME => $validated[Person::FIRST_NAME] ?? Person::DEFAULT_FIRST_NAME,
                Person::LAST_NAME => $validated[Person::LAST_NAME] ?? Person::DEFAULT_LAST_NAME,
                Person::PHONE_NUMBER => $validated[Person::PHONE_NUMBER] ?? Person::DEFAULT_PHONE_NUMBER,
            ]
        );
    }
}
