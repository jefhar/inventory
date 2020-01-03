<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use Domain\Products\Models\Product;

/**
 * Class ProductShowAction
 *
 * @package Domain\Products\Actions
 */
class ProductShowAction
{

    /**
     * @param Product $product
     * @return array
     */
    public static function execute(Product $product): array
    {
        // Make $formData and $values both arrays:
        $formData = json_decode($product->type->form, true, 512, JSON_THROW_ON_ERROR);
        $values = $product->values; // serial => '12345'

        // Pass $field by reference so it can be modified.
        foreach ($formData as &$field) {
            $attribute = $field['name'];
            if (\array_key_exists($attribute, $values)) {
                $field['userData'] = json_encode([$values[$attribute]], JSON_THROW_ON_ERROR, 512);
            }
        }

        return $formData;
    }
}
