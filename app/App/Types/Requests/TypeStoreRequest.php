<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Types\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TypeStoreRequest
 *
 * @package App\Types\Requests
 */
class TypeStoreRequest extends FormRequest
{
    private const RULES = [
        self::FORCE => ['boolean', 'nullable'],
        self::FORM => ['required', 'json'],
        self::NAME => ['required', 'string'],
    ];
    public const FORCE = 'force';
    public const FORM = 'form';
    public const NAME = 'name';

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
