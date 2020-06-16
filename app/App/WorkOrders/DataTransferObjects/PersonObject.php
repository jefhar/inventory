<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Domain\WorkOrders\Models\Person;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class PersonObject
 *
 * @package App\WorkOrders\DataTransferObjects
 */
class PersonObject extends DataTransferObject
{
    public const EMAIL = Person::EMAIL;
    public const FIRST_NAME = Person::FIRST_NAME;
    public const LAST_NAME = Person::LAST_NAME;
    public const PHONE_NUMBER = Person::PHONE_NUMBER;
    public string $email = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $phone_number = '';

    /**
     * @param array $validated
     * @return PersonObject
     */
    public static function fromRequest(array $validated): PersonObject
    {
        return new self(
            [
                self::EMAIL => $validated[self::EMAIL] ?? '',
                self::FIRST_NAME => $validated[self::FIRST_NAME] ?? '',
                self::LAST_NAME => $validated[self::LAST_NAME] ?? '',
                self::PHONE_NUMBER => $validated[self::PHONE_NUMBER] ?? '',
            ]
        );
    }
}
