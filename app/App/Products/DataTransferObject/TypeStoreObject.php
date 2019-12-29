<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use Domain\Products\Models\Type;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class TypeStoreObject
 *
 * @package App\Products\DataTransferObject
 */
class TypeStoreObject extends DataTransferObject
{
    public bool $force;
    public string $form;
    public string $name;

    /**
     * @param array $validated
     * @return TypeStoreObject
     */
    public static function fromRequest(array $validated): TypeStoreObject
    {
        return new self(
            [
                'force' => (bool)($validated['force'] ?? false),
                Type::FORM => $validated[Type::FORM],
                Type::NAME => $validated[Type::NAME],
            ]
        );
    }
}
