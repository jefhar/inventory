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
    private const LUHN = 'luhn';

    public function toArray($request)
    {
        $translation = [
            self::CLIENT_COMPANY_NAME => self::CLIENT_COMPANY_NAME,
            self::EMAIL => self::EMAIL,
            self::FIRST_NAME => self::FIRST_NAME,
            self::INTAKE => self::INTAKE,
            self::IS_LOCKED => self::IS_LOCKED,
            self::LUHN => self::ID,
            self::PHONE_NUMBER => self::PHONE_NUMBER,
            self::LAST_NAME => self::LAST_NAME,
        ];

        $json = collect();
        foreach ($this->resource as $key => $value) {
            if ($key === self::ID || $value === '') {
                continue;
            }
            $json = $json->merge([$translation[$key] => $value]);
        }

        return $json->toArray();
    }
}
