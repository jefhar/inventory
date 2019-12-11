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
 * Class Type
 *
 * @package Domain\Products\Models
 *
 * @method static Type select(?mixed $columns = null, ?mixed $column = null)
 * @method static Type where(mixed $field, ?mixed $value = null, ?mixed $value = null)
 * @method Type first()
 * @method Type get()
 * @property string $name
 */
class Type extends Model
{
    public const FORM = 'form';
    public const ID = 'id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const TABLE = 'types';

    protected $table = self::TABLE;

    /**
     * @param string $name
     */
    public function setNameAttribute(string $name): void
    {
        $this->attributes[self::SLUG] = Str::slug($name);
        $this->attributes[self::NAME] = Str::title($name);
        parent::select();
    }

    /**
     * This allows for matching the model by the slug in the path
     *
     * @return string
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return self::SLUG;
    }
}
