<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Domain\WorkOrders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Person
 *
 * @package Domain\WorkOrders
 *
 * @property int $client_id
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static findOrFail(array $array)
 * @method static where(string $field, string $value)
 */
class Person extends Model
{
    public const CLIENT_ID = 'client_id';
    public const EMAIL = 'email';
    public const FIRST_NAME = 'first_name';
    public const ID = 'id';
    public const LAST_NAME = 'last_name';
    public const PHONE_NUMBER = 'phone_number';
    public const TABLE = 'people';
    public $fillable = [
        self::EMAIL,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::PHONE_NUMBER,
    ];
    public $table = self::TABLE;
    protected $attributes = [
        self::PHONE_NUMBER => '000-000-0000',
        self::FIRST_NAME => 'first',
    ];

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumberAttribute(string $phoneNumber): void
    {
        $this->attributes[self::PHONE_NUMBER] = self::unformatPhoneNumber($phoneNumber);
    }

    /**
     * @param string $phoneNumber
     * @return string
     */
    public static function unformatPhoneNumber(string $phoneNumber): string
    {
        return preg_replace("/[^\d]/", '', $phoneNumber);
    }

    /**
     * @param string $phoneNumber
     * @return string
     */
    public function getPhoneNumberAttribute(string $phoneNumber): string
    {
        return static::formatPhoneNumber($phoneNumber);
    }

    /**
     * @param string $phoneNumber
     * @return string
     */
    public static function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = static::unformatPhoneNumber($phoneNumber);
        if (empty($phoneNumber)) {
            $phoneNumber = '0000000000';
        }
        preg_match("/(\d\d\d)(\d\d\d)(\d\d\d\d)(\d*)/", $phoneNumber, $outputArray);
        $output = '(' . $outputArray[1] . ') ' . $outputArray[2] . '-' . $outputArray[3];
        if ($outputArray[4] !== '') {
            $output .= ' x' . $outputArray[4];
        }

        return $output;
    }
}
