<?php

/**
 * PHP Version 7.2
 *
 * servicesAndGoods
 */

declare(strict_types=1);

namespace Domain\WorkOrders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Class Client
 *
 * @package Domain\WorkOrders
 * @property int $id
 * @property string $company_name
 * @property string $first_name
 * @property string $last_name
 * @property string $primary_phone
 * @property Person $person
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Model|static find($value)
 * @method static Model|static firstOrCreate($value)
 * @method static Model|static where(string $field, string $value, string $value = null)
 * @method static Model|static first()
 * @method static Model|static firstOrNew(array $array)
 * @method static Model|static whereIn(string $ID, Collection $client_ids)
 * @method static Model|static with(string $relationship)
 * @method static Builder inRandomOrder()
 */
class Client extends Model
{
    public const COMPANY_NAME = 'company_name';
    public const ID = 'id';
    public const TABLE = 'clients';

    public $fillable = [
        self::COMPANY_NAME,
    ];
    public $table = self::TABLE;

    /**
     * @return HasOne
     */
    public function person(): HasOne
    {
        return $this->hasOne(Person::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}
