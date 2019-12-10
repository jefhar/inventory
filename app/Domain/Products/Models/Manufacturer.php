<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Manufacturer
 *
 * @package Domain\Products\Models
 *
 * @method static Manufacturer firstOrCreate(array $array)
 * @property int id
 * @property string $name
 */
class Manufacturer extends Model
{
    public const ID = 'id';
    public const NAME = 'name';
    public const TABLE = 'manufacturers';

    public $fillable = [
        self::NAME,
    ];
    public $table = self::TABLE;
}
