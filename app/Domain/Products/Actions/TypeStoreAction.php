<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Products\DataTransferObject\TypeStoreObject;
use Domain\Products\Models\Type;
use Illuminate\Support\Str;

/**
 * Class TypeStoreAction
 *
 * @package Domain\Products\Actions
 */
class TypeStoreAction
{

    /**
     * @param TypeStoreObject $typeStoreObject
     * @return Type
     */
    public static function execute(TypeStoreObject $typeStoreObject): Type
    {
        $type = Type::updateOrCreate(
            [
                Type::NAME => Str::title($typeStoreObject->name),
            ],
            [
                Type::SLUG => Str::slug($typeStoreObject->name),
                Type::FORM => $typeStoreObject->form,
            ]
        );

        return $type;
    }
}
