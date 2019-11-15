<?php

use App\Admin\Controllers\WorkOrdersController;
use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create(
            $tableNames['permissions'],
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
            }
        );

        Schema::create(
            $tableNames['roles'],
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
            }
        );

        Schema::create(
            $tableNames['model_has_permissions'],
            function (Blueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger('permission_id');

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index(
                    [$columnNames['model_morph_key'], 'model_type',],
                    'model_has_permissions_model_id_model_type_index'
                );

                $table->foreign('permission_id')
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->primary(
                    ['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        );

        Schema::create(
            $tableNames['model_has_roles'],
            function (Blueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger('role_id');

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index(
                    [$columnNames['model_morph_key'], 'model_type',],
                    'model_has_roles_model_id_model_type_index'
                );

                $table->foreign('role_id')
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->primary(
                    ['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        );

        Schema::create(
            $tableNames['role_has_permissions'],
            function (Blueprint $table) use ($tableNames) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
            }
        );

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        $this->addRolesAndPermissions();
    }

    /**
     *
     */
    private function addRolesAndPermissions()
    {
        // 1. Clear the cache:
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create permissions:
        $workOrderOptionalPerson = Permission::create(['name' => UserPermissions::WORK_ORDER_OPTIONAL_PERSON]);
        $workOrderStore = Permission::create(['name' => WorkOrdersController::STORE_NAME]);
        $workOrderCreate = Permission::create(['name' => WorkOrdersController::CREATE_NAME]);

        // 3. Create Roles
        // SuperAdmin
        $superAdmin = Role::create(['name' => UserRoles::SUPER_ADMIN]);
        $superAdmin->givePermissionTo(Permission::all());

        // Owner
        $owner = Role::create(['name' => UserRoles::OWNER]);
        $owner->givePermissionTo(
            $workOrderStore,
            $workOrderCreate
        );

        // SalesRep
        $salesRep = Role::create(['name' => UserRoles::SALES_REP]);
        $salesRep->givePermissionTo(
            $workOrderStore,
            $workOrderCreate,
            );

        // Technician
        $tech = Role::create(['name' => UserRoles::TECHNICIAN]);
        $tech->givePermissionTo(
            $workOrderOptionalPerson,
            $workOrderStore,
            $workOrderCreate
        );

        // Employee
        // $employee = Role::create(['name' => UserRoles::EMPLOYEE]);

        // Shredder
        // $shredder = Role::create(['name' => UserRoles::SHREDDER]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}