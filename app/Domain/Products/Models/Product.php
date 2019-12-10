<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Models;

use Domain\WorkOrders\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Product
 *
 * @package Domain\Products\Models
 *
 * @property mixed $values
 */
class Product extends Model
{
    public const ID = 'id';
    public const MANUFACTURER_ID = 'manufacturer_id';
    public const MODEL = 'model';
    public const TABLE = 'products';
    public const TYPE_ID = 'type_id';
    public const VALUES = 'values';
    public const WORK_ORDER_ID = 'work_order_id';

    protected $fillable = [
        self::MODEL,
    ];

    protected $table = self::TABLE;

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
}
