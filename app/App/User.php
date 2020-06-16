<?php

declare(strict_types=1);

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @method hasPermissionTo($permission, $guardName = null) : bool
 * @method assignRole(...$roles) : User
 * @method givePermissionTo(...$permissions) : User
 * @method revokePermissionTo($permission) : User
 * @method static User create(array $array)
 */
class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    public const EMAIL = 'email';
    public const ID = 'ID';
    public const NAME = 'name';
    public const PASSWORD = 'password';
    public const TABLE = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = self::TABLE;
}
