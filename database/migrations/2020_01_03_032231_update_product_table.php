<?php

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use Domain\Products\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tdely\Luhn\Luhn;

class UpdateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            Product::TABLE,
            function (Blueprint $table) {
                $table->unsignedBigInteger(Product::LUHN)
                    ->nullable()
                    ->after(Product::WORK_ORDER_ID);
                $table->string(Product::STATUS, 64)
                    ->default(Product::STATUS_AVAILABLE)
                    ->after(Product::MODEL);
                $table->string(Product::SERIAL, 64)
                    ->nullable()
                    ->after(Product::MODEL);
                $table->index(Product::SERIAL);
            }
        );

        $products = Product::all();
        foreach ($products as $product) {
            $product->luhn = Luhn::create($product->id);
            $product->save();
        }

        $editSavedProduct = Permission::create(['name' => UserPermissions::EDIT_SAVED_PRODUCT]);

        $owner = Role::findByName(UserRoles::OWNER);
        $superAdmin = Role::findByName(UserRoles::SUPER_ADMIN);
        $salesRep = Role::findByName(UserRoles::SALES_REP);

        $owner->givePermissionTo($editSavedProduct);
        $superAdmin->givePermissionTo($editSavedProduct);
        $salesRep->givePermissionTo($editSavedProduct);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'products',
            function (Blueprint $table) {
            }
        );
    }
}
