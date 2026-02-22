<?php

namespace Pantono\Cart;

use Pantono\Cart\Repository\OrdersRepository;
use Pantono\Hydrator\Hydrator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Pantono\Cart\Model\Order;
use Pantono\Cart\Filter\OrderFilter;
use Pantono\Payments\Model\Payment;
use Pantono\Cart\Event\PreOrderSaveEvent;
use Pantono\Cart\Event\PostOrderSaveEvent;

class Orders
{
    private OrdersRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;
    public const int LINE_TYPE_PRODUCT = 1;
    public const int LINE_TYPE_DELIVERY = 2;
    public const int LINE_TYPE_DISCOUNT = 2;
    public const int LINE_STATUS_PENDING = 1;
    public const int LINE_STATUS_DISPATCHED = 2;

    public function __construct(OrdersRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->hydrator->lookupRecord(Order::class, $id);
    }

    /**
     * @param OrderFilter $filter
     * @return Order[]
     */
    public function getOrdersByFilter(OrderFilter $filter): array
    {
        return $this->hydrator->hydrateSet(Order::class, $this->repository->getOrdersByFilter($filter));
    }

    /**
     * @param Order $order
     * @return Payment[]
     */
    public function getPaymentsForOrder(Order $order): array
    {
        return $this->hydrator->hydrateSet(Payment::class, $this->repository->getPaymentsForOrder($order));
    }

    public function saveOrder(Order $order): void
    {
        $previous = $order->getId() ? $this->getOrderById($order->getId()) : null;
        $event = new PreOrderSaveEvent();
        $event->setCurrent($order);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveModel($order);

        $event = new PostOrderSaveEvent();
        $event->setCurrent($order);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }
}
