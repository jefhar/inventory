<?php

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $createCart = Permission::create(['name' => UserPermissions::MUTATE_CART]);
        $createOrEditProductType = Permission::create(['name' => UserPermissions::CREATE_OR_EDIT_PRODUCT_TYPE]);
        $seeAllCarts = Permission::create(['name' => UserPermissions::SEE_ALL_OPEN_CARTS]);
        $updateProductPrice = Permission::create(['name' => UserPermissions::UPDATE_PRODUCT_PRICE]);
        $updateRawProducts = Permission::create(['name' => UserPermissions::UPDATE_RAW_PRODUCTS]);

        $owner = Role::findByName(UserRoles::OWNER);
        $superAdmin = Role::findByName(UserRoles::SUPER_ADMIN);
        $salesRep = Role::findByName(UserRoles::SALES_REP);
        $technician = Role::findByName(UserRoles::TECHNICIAN);

        $owner->givePermissionTo(
            [$createCart, $createOrEditProductType, $seeAllCarts, $updateProductPrice, $updateRawProducts]
        );
        $superAdmin->givePermissionTo(
            [$createCart, $createOrEditProductType, $seeAllCarts, $updateProductPrice, $updateRawProducts]
        );
        $salesRep->givePermissionTo([$updateProductPrice, $createCart]);
        $technician->givePermissionTo([$createOrEditProductType, $updateRawProducts]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
