<?php

declare(strict_types=1);

namespace App;

use Closure;
use Domain\Carts\Models\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @method bool hasPermissionTo($permission, $guardName = null)
 * @method static Builder inRandomOrder()
 * @method static User create(array $array)
 * @method static User firstOrCreate(array $array, array $values = [])
 * @method static User where(Closure|string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method User assignRole(...$roles)
 * @method User first()
 * @method User givePermissionTo(...$permissions)
 * @method User revokePermissionTo($permission)
 * @property Cart $carts
 * @property int $id
 * @property string $email
 * @property string $name
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

    /**
     * @return HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
