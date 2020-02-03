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
        if ($user->can(UserPermissions::EDIT_SAVED_PRODUCT)) {
            $className = 'form-control';
            $readonly = 'false';
            $disabled = 'false';
        } else {
            $className = 'form-control-plaintext';
            $readonly = 'readonly';
            $disabled = 'disabled';
        }
        // Make $formData and $values both arrays:
        $formData = json_decode($product->type->form, true, 512, JSON_THROW_ON_ERROR);
        $values = $product->values;

        // Pass $field by reference so it can be modified.
        foreach ($formData as &$field) {
            $attribute = $field['name'];
            $field['readonly'] = $readonly;
            $field['className'] = $className;
            $field['disabled'] = $disabled;
            if (\array_key_exists($attribute, $values)) {
                $field['userData'] = [$values[$attribute]];
            }
        }
        // Warning:[EA] This variable must be unset just after foreach to prevent possible side-effects.
        unset($field);

        // Add manufacturer and model to $formData
        $manufacturer = [
            'className' => $className,
            'label' => 'Manufacturer',
            'name' => 'manufacturer',
            'readonly' => $readonly,
            'subtype' => 'text',
            'type' => 'text',
            'userData' => [$product->manufacturer->name],
        ];
        $model = [
            'className' => $className,
            'label' => 'Model',
            'name' => 'model',
            'readonly' => $readonly,
            'subtype' => 'text',
            'type' => 'text',
            'userData' => [$product->model],
        ];
        array_unshift($formData, $manufacturer, $model);

        return $formData;
    }
}
