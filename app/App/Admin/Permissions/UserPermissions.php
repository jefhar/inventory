<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\Admin\Permissions;

/**
 * Class UserPermissions
 *
 * @package App\Admin\Permissions
 */
final class UserPermissions
{
    public const EDIT_SAVED_PRODUCT = 'inventoryItem.view.edit';
    public const IS_EMPLOYEE = 'user.is.employee';
    public const MUTATE_CART = 'cart.mutate';
    public const UPDATE_PRODUCT_PRICE = 'product.price.update';
    public const UPDATE_RAW_PRODUCTS = 'product.raw.update';
    public const WORK_ORDER_OPTIONAL_PERSON = 'workOrder.optional.person';
}
