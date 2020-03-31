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
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class Product
 *
 * @package Domain\Products\Models
 *
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder whereIn(string $ID, Collection $product_ids)
 * @method static LengthAwarePaginator paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static Model|EloquentCollection|static[]|static|null find(int $int)
 * @method static Product findOrFail($input)
 * @property array $values
 * @property int|null $cart_id
 * @property int $id
 * @property int $luhn
 * @property int $price
 * @property Manufacturer $manufacturer;
 * @property string $model
 * @property string $serial
 * @property Type $type
 * @property WorkOrder $workOrder
 */
class Product extends Model
{
    public const CART_ID = 'cart_id';
    public const ID = 'id';
    public const LUHN = 'luhn';
    public const MANUFACTURER_ID = 'manufacturer_id';
    public const MODEL = 'model';
    public const PRICE = 'price';
    public const SERIAL = 'serial';
    public const STATUS = 'status';
    public const STATUS_AVAILABLE = 'Available';
    public const TABLE = 'products';
    public const TYPE_ID = 'type_id';
    public const VALUES = 'values';
    public const WORK_ORDER_ID = 'work_order_id';

    protected $attributes = [
        self::STATUS => self::STATUS_AVAILABLE,
    ];
    protected $casts = [
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
