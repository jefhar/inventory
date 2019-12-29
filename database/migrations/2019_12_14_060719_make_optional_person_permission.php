<?php

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class MakeOptionalPersonPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $optionalPerson = Permission::create(['name' => UserPermissions::WORK_ORDER_OPTIONAL_PERSON]);

        $owner = \Spatie\Permission\Models\Role::findByName(UserRoles::OWNER);
        $superAdmin = \Spatie\Permission\Models\Role::findByName(UserRoles::SUPER_ADMIN);
        $tech = \Spatie\Permission\Models\Role::findByName(UserRoles::TECHNICIAN);

        $owner->givePermissionTo($optionalPerson);
        $superAdmin->givePermissionTo($optionalPerson);
        $tech->givePermissionTo($optionalPerson);
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
