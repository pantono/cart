<?php

namespace Pantono\Cart\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pantono\Cart\Event\PreAddProductToCartEvent;
use Pantono\Cart\ShoppingCart;
use Pantono\Cart\Exception\NotEnoughStock;

class CheckProductStockLevels implements EventSubscriberInterface
{
    private ShoppingCart $cart;

    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreAddProductToCartEvent::class => [
                ['checkStockLevels', 255]
            ]
        ];
    }

    public function checkStockLevels(PreAddProductToCartEvent $event): void
    {
        $product = $event->getVersion()->getParentProduct();

        $stockHolding = $product->getStockHolding();
        $pending = $this->cart->getStockReservationCountForProductId($product->getId());

        if ($pending + $event->getQuantity() > $stockHolding) {
            throw new NotEnoughStock('Sorry, not enough stock available');
        }
    }
}
