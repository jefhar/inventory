<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions;

use Domain\WorkOrders\Client;
use Symfony\Component\HttpFoundation\Response;

class Search
{

    /**
     * @param string $field ENUM {Client::COMPANY_NAME|}
     * @param $searchString
     * @return array
     */
    public static function findBy(string $field, $searchString): array
    {
        return [];
    }
}
