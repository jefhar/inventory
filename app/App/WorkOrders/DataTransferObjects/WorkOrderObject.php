<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class WorkOrderUpdateObject
 *
 * @package App\WorkOrders\DataTransferObjects
 */
class WorkOrderObject extends DataTransferObject
{
    public ?bool $is_locked = null;
    public ?string $intake = null;
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';

    /**
     * @param array $validated
     * @return WorkOrderObject
     */
    public static function fromRequest(array $validated): WorkOrderObject
    {
        return new self(
            [
                self::INTAKE => $validated[self::INTAKE] ?? null,
                self::IS_LOCKED => $validated[self::IS_LOCKED] ?? null,
            ]
        );
    }
}
