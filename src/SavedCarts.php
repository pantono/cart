<?php

namespace Pantono\Cart;

use Pantono\Cart\Repository\SavedCartsRepository;
use Pantono\Hydrator\Hydrator;
use Pantono\Cart\Model\SavedCart;
use Pantono\Cart\Event\PreSavedCartSaveEvent;
use Pantono\Cart\Event\PostSavedCartSaveEvent;
use Pantono\Cart\Filter\SavedCartFilter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SavedCarts
{
    private SavedCartsRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;

    public function __construct(SavedCartsRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher)
    {

        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }

    public function saveSavedCart(SavedCart $cart): void
    {
        $previous = $cart->getId() ? $this->hydrator->lookupRecord(SavedCart::class, $cart->getId()) : null;
        $event = new PreSavedCartSaveEvent();
        $event->setPrevious($previous);
        $event->setCurrent($cart);
        $this->dispatcher->dispatch($event);

        $this->repository->saveCart($cart);

        $event = new PostSavedCartSaveEvent();
        $event->setPrevious($previous);
        $event->setCurrent($cart);
        $this->dispatcher->dispatch($event);
    }

    /**
     * @param SavedCartFilter $filter
     * @return SavedCart[]
     */
    public function getSavedCartsByFilter(SavedCartFilter $filter): array
    {
        return $this->hydrator->hydrateSet(SavedCart::class, $this->repository->getSavedCartsByFilter($filter));
    }
}
