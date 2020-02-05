<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Models;

use Domain\Carts\Events\CartCreated;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Cart
 *
 * @package Domain\Carts\Models
 * @method static Builder where(string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Cart findOrFail($input)
 * @property Client $client
 * @property int $id
 * @property int $luhn
 * @property string $status
 */
class Cart extends Model
{
    use SoftDeletes;

    public const CLIENT_ID = 'client_id';
    public const ID = 'id';
    public const LUHN = 'luhn';
    public const STATUS = 'status';
    public const STATUS_OPEN = 'open';
    public const STATUS_VOID = 'void';
    public const TABLE = 'carts';
    public const USER_ID = 'user_id';

    public $with = ['client.person'];

    protected $attributes = [
        self::STATUS => self::STATUS_OPEN,
    ];

    protected $dispatchesEvents = [
        'created' => CartCreated::class,
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return self::LUHN;
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
