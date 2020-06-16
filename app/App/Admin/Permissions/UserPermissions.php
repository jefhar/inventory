<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\Admin\Permissions;

/**
 * Class UserPermissions
 *
 * @property string $name
 * @package App\Admin\Permissions
 */
final class UserPermissions
{
    public const CREATE_OR_EDIT_PRODUCT_TYPE = 'product.type.create_or_edit';
    public const CREATE_OR_EDIT_USERS = 'dashboard.use';
    public const IS_EMPLOYEE = 'user.is.employee';
    public const MUTATE_CART = 'cart.mutate';
    public const MUTATE_PRODUCT_VALUES = 'inventoryItem.view.edit';
    public const SEE_ALL_OPEN_CARTS = 'carts.view.all_open';
    public const UPDATE_PRODUCT_PRICE = 'product.price.update';
    public const UPDATE_RAW_PRODUCTS = 'product.raw.update';
    public const WORK_ORDER_OPTIONAL_PERSON = 'workOrder.optional.person';
    public const PERMISSIONS = [
        self::CREATE_OR_EDIT_PRODUCT_TYPE => 'Add or Change Product Types',
        self::CREATE_OR_EDIT_USERS => 'Create or Edit Users',
        self::IS_EMPLOYEE => 'Minimum Employee Permission',
        self::MUTATE_CART => 'Change Shopping Cart',
        self::MUTATE_PRODUCT_VALUES => 'Add Product to carts',
        self::SEE_ALL_OPEN_CARTS => 'View All Open Shopping Carts',
        self::UPDATE_PRODUCT_PRICE => 'Change Product Price',
        self::UPDATE_RAW_PRODUCTS => 'Modify a Product',
        self::WORK_ORDER_OPTIONAL_PERSON => 'Short Create Work Order',
    ];
    public const EMPLOYEE_DEFAULT_PERMISSIONS = [
        [self::IS_EMPLOYEE],
    ];
    public const SALES_REP_DEFAULT_PERMISSIONS = [
        [self::IS_EMPLOYEE],
        [self::UPDATE_PRODUCT_PRICE],
        [self::MUTATE_CART],
        [self::MUTATE_PRODUCT_VALUES],
    ];
    public const TECHNICIAN_DEFAULT_PERMISSIONS = [
        [self::CREATE_OR_EDIT_PRODUCT_TYPE],
        [self::IS_EMPLOYEE],
        [self::UPDATE_RAW_PRODUCTS],
        [self::WORK_ORDER_OPTIONAL_PERSON],
    ];
    public const OWNER_DEFAULT_PERMISSIONS = [
        [self::CREATE_OR_EDIT_PRODUCT_TYPE],
        [self::CREATE_OR_EDIT_USERS],
        [self::IS_EMPLOYEE],
        [self::MUTATE_CART],
        [self::MUTATE_PRODUCT_VALUES],
        [self::SEE_ALL_OPEN_CARTS],
        [self::UPDATE_PRODUCT_PRICE],
        [self::UPDATE_RAW_PRODUCTS],
        [self::WORK_ORDER_OPTIONAL_PERSON],
    ];
}
