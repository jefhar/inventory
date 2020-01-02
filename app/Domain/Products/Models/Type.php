<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Domain\Products\Events\TypeCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * Class Type
 *
 * @package Domain\Products\Models
 *
 * @method static Builder orderBy(string $slug)
 * @method static Type select(?mixed $columns = null, ?mixed $column = null)
 * @method static Type updateOrCreate(array $attributes, array $values = [])
 * @method static Type where(mixed $field, ?mixed $value = null, ?mixed $value = null)
 * @method Type first()
 * @method Type get()
 * @property string $form
 * @property string $name
 * @property string $slug
 */
class Type extends Model
{
    use SoftDeletes;

    public const FORM = 'form';
    public const ID = 'id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const TABLE = 'types';

    protected $table = self::TABLE;

    protected $dispatchesEvents = [
        'created' => TypeCreated::class,
    ];

    protected $fillable = [
        self::NAME,
        self::FORM,
    ];

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