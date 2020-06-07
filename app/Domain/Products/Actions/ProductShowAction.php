<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Admin\Permissions\UserPermissions;
use App\User;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\Auth;

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
     * @throws \JsonException
     */
    public static function execute(Product $product): array
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->can(UserPermissions::MUTATE_PRODUCT_VALUES)) {
            $className = 'form-control';
            $disabled = false;
        } else {
            $className = 'form-control-plaintext';
            $disabled = true;
        }
        // Convert both $formData and $values into arrays:
        $formData = json_decode($product->type->form, true, 512, JSON_THROW_ON_ERROR);
        $values = $product->values;

        // Pass $field by reference so it can be modified in place.
        foreach ($formData as &$field) {
            $attribute = $field['name'];
            $field['className'] = $className;
            $field['disabled'] = $disabled;
            if ($disabled) {
                $field['disabled'] = true;
            }
            if (array_key_exists($attribute, $values)) {
                $field['userData'] = [$values[$attribute]];
            }
        }
        unset($field);

        // Add manufacturer and model to $formData
        $manufacturer = [
            'className' => $className,
            'label' => 'Manufacturer',
            'name' => Product::MANUFACTURER_NAME,
            'subtype' => 'text',
            'type' => 'text',
            'userData' => [$product->manufacturer->name],
        ];
        $model = [
            'className' => $className,
            'label' => 'Model',
            'name' => Product::MODEL,
            'subtype' => 'text',
            'type' => 'text',
            'userData' => [$product->model],
        ];

        if ($disabled) {
            $model['disabled'] = $disabled;
            $manufacturer['disabled'] = $disabled;
        }
        array_unshift($formData, $manufacturer, $model);

        return $formData;
    }
}
