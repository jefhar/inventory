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
 * @method Client get()
 * @method Collection pluck(string $column, string|null $key = null)
 * @method static Builder inRandomOrder()
 * @method static Builder whereIn(string $ID, Collection $client_ids)
 * @method static Client find($value)
 * @method static Client first()
 * @method static Client firstOrCreate($value)
 * @method static Client firstOrNew(array $array)
 * @method static Client where(string $field, ?string $string = null, ?string $string1 = null)
 * @method static Client with(string $relationship)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property int|null $person_count
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
