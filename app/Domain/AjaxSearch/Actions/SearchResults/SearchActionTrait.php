<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

/**
 * Trait SearchActionTrait
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
trait SearchActionTrait
{

    /**
     * @return SearchActionInterface
     */
    public static function getInstance(): SearchActionInterface
    {
        return new self();
    }
}
