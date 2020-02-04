<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\DataTransferObjects;

use Domain\Carts\Models\Cart;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class CartPatchObject
 *
 * @package App\Carts\DataTransferObjects
 */
class CartPatchObject extends DataTransferObject
{
    public string $status;

    /**
     * @param array $validated
     * @return CartPatchObject
     */
    public static function fromRequest(array $validated): CartPatchObject
    {
        return new self([Cart::STATUS => $validated[Cart::STATUS]]);
    }
}
