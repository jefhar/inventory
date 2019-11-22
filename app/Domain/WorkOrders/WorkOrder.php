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
 */
class WorkOrder extends Model
{
    public const CLIENT_ID = 'client_id';
    public const ID = 'id';
    public const TABLE = 'workorders';
    public const USER_ID = 'user_id';
    public $table = self::TABLE;
    public $with = ['client.person', 'user'];

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
