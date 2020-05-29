<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Support;

class Luhn extends \Tdely\Luhn\Luhn
{

    public static function unLuhn(int $luhn)
    {
        return intdiv($luhn, 10);
    }
}
