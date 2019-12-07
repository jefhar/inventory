<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static select(string $SLUG, string $NAME)
 */
class Type extends Model
{
    public const ID = 'id';
    public const NAME = 'name';
    public const TABLE = 'types';
    public const SLUG = 'slug';
    public const FORM = 'form';

    protected $table = self::TABLE;

    /**
     * @param string $name
     */
    public function setNameAttribute(string $name): void
    {
        $this->attributes[self::SLUG] = Str::slug($name);
        $this->attributes[self::NAME] = $name;
    }

    /**
     * This allows for matching the model by the slug in the path
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return self::SLUG;
    }
}
