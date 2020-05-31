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
    public const CREATE_OR_EDIT_PRODUCT_TYPE = 'product.type.create_or_edit';
    public const EDIT_SAVED_PRODUCT = 'inventoryItem.view.edit';
    public const IS_EMPLOYEE = 'user.is.employee';
    public const MUTATE_CART = 'cart.mutate';
    public const SEE_ALL_OPEN_CARTS = 'carts.view.all_open';
    public const UPDATE_PRODUCT_PRICE = 'product.price.update';
    public const UPDATE_RAW_PRODUCTS = 'product.raw.update';
    public const WORK_ORDER_OPTIONAL_PERSON = 'workOrder.optional.person';
}
