<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Products\Model\ProductVersion;
use Pantono\Cart\Model\Cart;

abstract class AbstractAddProductToCartEvent extends Event
{
    private ProductVersion $version;
    private Cart $cart;
    private int $quantity;

    public function getVersion(): ProductVersion
    {
        return $this->version;
    }

    public function setVersion(ProductVersion $version): void
    {
        $this->version = $version;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
