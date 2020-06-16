<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Types\Requests;

use Domain\Products\Models\Type;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TypeStoreRequest
 *
 * @package App\Types\Requests
 */
class TypeStoreRequest extends FormRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            Type::NAME => ['required', 'string'],
            Type::FORM => ['required', 'json'],
            'force' => ['boolean', 'nullable'],
        ];
    }
}
