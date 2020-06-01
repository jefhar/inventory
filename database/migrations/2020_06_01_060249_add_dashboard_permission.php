<?php

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddDashboardPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $editUsers = Permission::create(['name' => UserPermissions::CREATE_OR_EDIT_USERS]);
        $owner = Role::findByName(UserRoles::OWNER);
        $superAdmin = Role::findByName(UserRoles::SUPER_ADMIN);

        $owner->givePermissionTo(
            [$editUsers]
        );
        $superAdmin->givePermissionTo(
            [$editUsers]
        );
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
