<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public const ID = 'id';
    public const NAME = 'name';
    public const TABLE = 'types';

    protected $table = self::TABLE;
}
