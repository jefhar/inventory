<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\AjaxSearch\DataTransferObjects;

use App\AjaxSearch\Requests\AjaxSearchRequest;
use Spatie\DataTransferObject\DataTransferObject;

class AjaxSearchObject extends DataTransferObject
{
    public string $field;
    public string $q;

    /**
     * @param string $field
     * @param array $validated
     * @return AjaxSearchObject
     */
    public static function fromRequest(string $field, array $validated): AjaxSearchObject
    {
        return new self(
            [
                AjaxSearchRequest::FIELD => $field,
                AjaxSearchRequest::Q => $validated[AjaxSearchRequest::Q],
            ]
        );
    }
}
