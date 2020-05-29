<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{

    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const EMAIL = 'email';
    public const FIRST_NAME = 'first_name';
    public const ID = 'id';
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';
    public const LAST_NAME = 'last_name';
    public const PHONE_NUMBER = 'phone_number';

    public function toArray($request)
    {
        return $this->resource;
    }
}
