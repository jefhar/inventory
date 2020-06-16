<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Listeners;

use Domain\Products\Events\TypeCreated;
use Illuminate\Support\Str;

/**
 * Class AddSlugToType
 *
 * @package Domain\Products\Listeners
 */
class AddSlugToType
{
    /**
     * @param TypeCreated $event
     */
    public function handle(TypeCreated $event): void
    {
        $type = $event->type;
        $type->slug = Str::slug($type->name);
        $type->name = Str::title($type->name);
    }
}
