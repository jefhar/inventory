<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
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
 *
 * @method Builder|static get()
 * @method static Builder inRandomOrder()
 * @method static Builder|static find($value)
 * @method static Builder|static where(string $field, string $value, string $value = null)
 * @method static Builder|static whereIn(string $ID, Collection $client_ids)
 * @method static Model|static first()
 * @method static Model|static firstOrCreate($value)
 * @method static Model|static firstOrNew(array $array)
 * @method static Model|static with(string $relationship)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property Person $person
 * @property string $company_name
 * @property string $first_name
 * @property string $last_name
 * @property string $primary_phone
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
     * @param string $searchString
     * @return Collection
     */
    public static function findByCompanyName(string $searchString): Collection
    {
        return self::where(self::COMPANY_NAME, 'like', '%' . $searchString . '%')
            ->get()
            ->pluck(self::ID, self::ID);
    }

    /**
     * @return HasOne
     */
    public function person(): HasOne
    {
        return $this->hasOne(Person::class);
    }

    /**
     * @return HasMany
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}
