<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Domain\Carts\Models\Cart;
use Domain\Products\Events\ProductCreated;
use Domain\Products\Events\ProductSaved;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class Product
 *
 * @package Domain\Products\Models
 *
 * @method Collection get(array|string $columns = ['*'])
 * @method Product first(array|string $columns = ['*'])
 * @method static Builder whereIn(string $ID, Collection $product_ids)
 * @method static Collection firstOrFail($columns = ['*'])
 * @method static LengthAwarePaginator paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static Product|null find(int $int)
 * @method static Product findOrFail(mixed $id, array $columns = ['*'])
 * @method static Product where($column, $operator = null, $value = null, $boolean = 'and')
 * @property array $values
 * @property int $id
 * @property int $luhn
 * @property int $price
 * @property int|null $cart_id
 * @property Manufacturer $manufacturer;
 * @property string $model
 * @property string $serial
 * @property string $status
 * @property Type $type
 * @property WorkOrder $workOrder
 */
class Product extends Model
{
    public const CART_ID = 'cart_id';
    public const ID = 'id';
    public const LUHN = 'luhn';
    public const MANUFACTURER_ID = 'manufacturer_id';
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const MODEL = 'model';
    public const PRICE = 'price';
    public const SERIAL = 'serial';
    public const STATUS = 'status';
    public const STATUS_AVAILABLE = 'Available';
    public const STATUS_IN_CART = 'In Cart';
    public const STATUS_INVOICED = 'Invoiced';
    public const TABLE = 'products';
    public const TYPE = 'type';
    public const TYPE_ID = 'type_id';
    public const VALUES = 'values';
    public const WORK_ORDER_ID = 'work_order_id';

    protected $attributes = [
        self::STATUS => self::STATUS_AVAILABLE,
        self::PRICE => 0,
    ];
    protected $casts = [
        self::PRICE => 'decimal:2',
        self::VALUES => 'array',
    ];
    protected $fillable = [
        self::MODEL,
        self::VALUES,
    ];
    protected $with = [
        'manufacturer',
    ];

    protected $table = self::TABLE;

    protected $dispatchesEvents = [
        'created' => ProductCreated::class,
        'saving' => ProductSaved::class,
    ];

    /**
     * Accessor to retrieve product price as decimal value.
     *
     * @param int $price
     * @return float
     */
    public function getPriceAttribute(int $price): float
    {
        return $price / 100;
    }

    /**
     * Mutator to store product price as integer.
     *
     * @param float $price
     */
    public function setPriceAttribute(float $price): void
    {
        if ($price < 0.01) {
            $price = 0.00;
        }

        $this->attributes[self::PRICE] = round($price * 100, 0);
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    public static function findBySerial(string $searchString): Collection
    {
        return self::where(self::SERIAL, 'like', '%' . $searchString . '%')
            ->get()
            ->pluck(self::ID, self::ID);
    }

    /**
     * This allows for matching the model by the slug in the path
     *
     * @return string
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return self::LUHN;
    }

    /**
     * @return BelongsTo
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * @return BelongsTo
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    /**
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
