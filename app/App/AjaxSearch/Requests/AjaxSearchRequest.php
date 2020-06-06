<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\AjaxSearch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjaxSearchRequest extends FormRequest
{

    public const SEARCH_COMPANY_NAME = 'company_name';
    public const SEARCH_MANUFACTURER = 'manufacturer';
    public const SEARCH_MODEL = 'model';

    public const FIELD = 'field';
    public const Q = 'q';

    public function rules(): array
    {
        return [
            self::Q => ['required'],
        ];
    }
}
