<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Models;

use App\User;
use Domain\Carts\Events\CartCreated;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Cart
 *
 * @method Cart first(array|string $columns = ['*'])
 * @method Collection get(array|string $columns = ['*'])
 * @method Collection orderBy($column, $direction = 'asc')
 * @method static Cart findOrFail(mixed $id, array $columns = ['*'])
 * @method static Cart where(string|array $column, mixed $operator = null, mixed $value = null, string $bool = 'and')
 * @method static Cart|null find($input)
 * @method static Collection firstOrFail($columns = ['*'])
 * @package Domain\Carts\Models
 * @property Client $client
 * @property int $id
 * @property int $luhn
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Database\Eloquent\Collection|Product[] $products
 */
class Cart extends Model
{
    use SoftDeletes;

    public const CLIENT_COMPANY_NAME = 'client_name';
    public const CLIENT_ID = 'client_id';
    public const ID = 'id';
    public const LUHN = 'luhn';
    public const STATUS = 'status';
    public const STATUS_INVOICED = 'invoiced';
    public const STATUS_OPEN = 'open';
    public const STATUS_VOID = 'void';
    public const STATUSES = [
        self::STATUS_INVOICED,
        self::STATUS_OPEN,
        self::STATUS_VOID,
    ];
    public const TABLE = 'carts';
    public const USER_ID = 'user_id';

    public $with = ['client.person'];

    protected $attributes = [
        self::STATUS => self::STATUS_OPEN,
    ];

    protected $dispatchesEvents = [
        'created' => CartCreated::class,
    ];

    protected $casts = [
        self::CLIENT_ID => 'integer',
        self::LUHN => 'integer',
        self::USER_ID => 'integer',
    ];

    /**
     * Include soft-deleted models
     *
     * @param string $value
     * @param ?string $field
     * @return Cart|Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return self::withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
