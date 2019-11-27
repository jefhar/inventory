<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WorkOrder
 *
 * @package Domain\WorkOrders
 * @property bool $is_locked
 * @property int $id
 * @property int $user_id
 * @property Client client
 * @property string|null intake
 * @method static Model|static where(mixed $field, ?string $value = null, ?string $value = null)
 * @method static paginate(int $int)
 * @method static Model|static whereNotIn(string $field, array $searchArray)
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
