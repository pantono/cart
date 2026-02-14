<?php

namespace Pantono\Cart;

use Pantono\Hydrator\Hydrator;
use Pantono\Cart\Repository\ShoppingCartRepository;
use Pantono\Cart\Model\Cart;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Pantono\Cart\Event\PreCartSaveEvent;
use Pantono\Cart\Event\PostCartSaveEvent;
use Pantono\Cart\Model\CartItem;
use Pantono\Products\Model\SpecialOffer;

class ShoppingCart
{
    private ShoppingCartRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;

    public function __construct(ShoppingCartRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }

    public function getActiveCartForSession(string $sessionId): Cart
    {
        return $this->hydrator->hydrate(Cart::class, $this->repository->getActiveCartForSession($sessionId));
    }

    /**
     * @return SpecialOffer[]
     */
    public function getActiveSpecialOffersForCartItem(CartItem $cartItem): array
    {
        return $this->hydrator->hydrateSet(SpecialOffer::class, $this->repository->getActiveSpecialOffersForProduct($cartItem->getProduct()->getId()));
    }

    public function saveCart(Cart $cart): void
    {
        $previous = $cart->getId() ? $this->hydrator->lookupRecord(Cart::class, $cart->getId()) : null;
        $event = new PreCartSaveEvent();
        $event->setCurrent($cart);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveCart($cart);

        $event = new PostCartSaveEvent();
        $event->setCurrent($cart);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

}
