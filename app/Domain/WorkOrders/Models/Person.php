<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Person
 *
 * @package Domain\WorkOrders\Models
 *
 * @method Collection map(callable $callback);
 * @method Person get()
 * @method static EloquentCollection pluck(string $column, ?string $key = null)
 * @method static Person findOrFail(array $array)
 * @method static Person orWhere(string $LAST_NAME, string $string, string $string1)
 * @method static Person where(string $column, ?string $string = null, ?string $string1 = null)
 * @method static Person whereIn(string $ID, $people_ids)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $client_id
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 */
class Person extends Model
{
    public const CLIENT_ID = 'client_id';
    public const DEFAULT_EMAIL = '';
    public const DEFAULT_FIRST_NAME = 'first';
    public const DEFAULT_LAST_NAME = 'last';
    public const DEFAULT_PHONE_NUMBER = '000-000-0000';
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
        self::EMAIL => self::DEFAULT_EMAIL,
        self::FIRST_NAME => self::DEFAULT_FIRST_NAME,
        self::LAST_NAME => self::DEFAULT_LAST_NAME,
        self::PHONE_NUMBER => self::DEFAULT_PHONE_NUMBER,
    ];

    /**
     * @param string $searchString
     * @return Collection
     */
    public static function findByName(string $searchString): Collection
    {
        return self::where(self::FIRST_NAME, 'like', '%' . $searchString . '%')
            ->orWhere(self::LAST_NAME, 'like', '%' . $searchString . '%')
            ->get()
            ->pluck(self::ID, self::ID);
    }

    /**
     * This is called when using `$person->phone_number = $phoneNumber;`
     *
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
     * This is called when using `$phoneNumber = $person->phone_number`
     *
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
