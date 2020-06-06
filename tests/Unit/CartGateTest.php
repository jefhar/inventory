<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Gates;
use App\Admin\Permissions\UserRoles;
use Domain\Carts\Models\Cart;
use Tests\TestCase;
use Tests\Traits\FullObjects;

class CartGateTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function cartBelongsToUser(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $firstCart = factory(Cart::class)->make();
        $secondCart = factory(Cart::class)->make();
        $salesRep->carts()->save($firstCart);
        $salesRep->carts()->save($secondCart);

        $otherRep = $this->createEmployee(UserRoles::SALES_REP);
        $otherRepCart = factory(Cart::class)->make();
        $otherRepSecondCart = factory(Cart::class)->make();
        $otherRep->carts()->save($otherRepCart);
        $otherRep->carts()->save($otherRepSecondCart);

        $this->assertTrue(Gates::cartBelongsToUser($firstCart, $salesRep));
        $this->assertTrue(Gates::cartBelongsToUser($secondCart, $salesRep));
        $this->assertFalse(Gates::cartBelongsToUser($firstCart, $otherRep));
        $this->assertFalse(Gates::cartBelongsToUser($secondCart, $otherRep));

        $this->assertTrue(Gates::cartBelongsToUser($otherRepCart, $otherRep));
        $this->assertTrue(Gates::cartBelongsToUser($otherRepSecondCart, $otherRep));
        $this->assertFalse(Gates::cartBelongsToUser($otherRepCart, $salesRep));
        $this->assertFalse(Gates::cartBelongsToUser($otherRepSecondCart, $salesRep));
    }
}
