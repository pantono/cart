<?php

namespace Pantono\Cart;

use Pantono\Cart\Repository\OrdersRepository;
use Pantono\Hydrator\Hydrator;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Orders
{
    private OrdersRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;

    public function __construct(OrdersRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }
}
