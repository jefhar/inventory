<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Events;

use Domain\Products\Models\Type;
use Illuminate\Queue\SerializesModels;

/**
 * Class TypeCreated
 *
 * @package Domain\Products\Events
 */
class TypeCreated
{
    use SerializesModels;

    public Type $type;

    /**
     * TypeCreated constructor.
     *
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
    }
}
