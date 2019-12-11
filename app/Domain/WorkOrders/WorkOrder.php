<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders;

use App\User;
use Domain\Products\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class WorkOrder
 *
 * @package Domain\WorkOrders
 *
 * @method static Collection paginate(int $int)
 * @method static WorkOrder findOrFail($get)
 * @method static WorkOrder where(mixed $field, ?mixed $value = null, ?mixed $value = null)
 * @method static WorkOrder whereNotIn(string $field, array $searchArray)
 * @property bool $is_locked
 * @property Client $client
 * @property int $id
 * @property int $user_id
 * @property string|null $intake
 */
class WorkOrder extends Model
{
    public const CLIENT_ID = 'client_id';
    public const ID = 'id';
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';
    public const TABLE = 'workorders';
    public const USER_ID = 'user_id';

    public $table = self::TABLE;
    public $with = ['client.person', 'user'];
    protected $attributes = [
        self::IS_LOCKED => false,
        self::INTAKE => '',
    ];
    protected $casts = [
        self::IS_LOCKED => 'boolean',
    ];

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

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
