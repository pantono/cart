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
use Pantono\Cart\Model\OrderStatus;
use Pantono\Cart\Model\OrderFolder;
use Pantono\Authentication\Model\User;
use Pantono\Cart\Model\OrderNote;
use Pantono\Cart\Event\PreOrderNoteSaveEvent;
use Pantono\Cart\Event\PostOrderNoteSaveEvent;
use Pantono\Cart\Model\OrderFlagType;

class Orders
{
    private OrdersRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;
    public const int LINE_TYPE_PRODUCT = 1;
    public const int LINE_TYPE_DELIVERY = 2;
    public const int LINE_TYPE_DISCOUNT = 3;
    public const int LINE_STATUS_PENDING = 1;
    public const int LINE_STATUS_DISPATCHED = 2;

    public const int ORDER_STATUS_PENDING = 1;
    public const int ORDER_STATUS_PREPARING = 2;
    public const int ORDER_STATUS_DISPATCHED = 3;
    public const int ORDER_STATUS_CANCELLED = 4;
    public const int ORDER_STATUS_PARTIALLY_DISPATCHED = 5;

    public function __construct(OrdersRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
    }

    public function getStatusById(int $id): ?OrderStatus
    {
        return $this->hydrator->lookupRecord(OrderStatus::class, $id);
    }

    public function getFolderById(int $id): ?OrderFolder
    {
        return $this->hydrator->lookupRecord(OrderFolder::class, $id);
    }

    /**
     * @return OrderFolder[]
     */
    public function getAllFolders(): array
    {
        return $this->hydrator->hydrateSet(OrderFolder::class, $this->repository->getAllFolders());
    }

    /**
     * @return OrderStatus[]
     */
    public function getAllStatuses(): array
    {
        return $this->hydrator->hydrateSet(OrderStatus::class, $this->repository->getAllStatuses());
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->hydrator->lookupRecord(Order::class, $id);
    }

    public function getOrderByRef(string $ref): ?Order
    {
        return $this->hydrator->hydrate(Order::class, $this->repository->getOrderByRef($ref));
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

        $this->repository->saveOrder($order);

        $event = new PostOrderSaveEvent();
        $event->setCurrent($order);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

    public function addNoteToOrder(Order $order, User $user, string $noteText): OrderNote
    {
        $note = new OrderNote();
        $note->setOrderId($order->getId());
        $note->setDate(new \DateTimeImmutable());
        $note->setUser($user);
        $note->setNote($noteText);

        $this->saveOrderNote($note);
        return $note;
    }

    public function saveOrderNote(OrderNote $note): void
    {
        $previous = $note->getId() ? $this->hydrator->lookupRecord(OrderNote::class, $note->getId()) : null;
        $event = new PreOrderNoteSaveEvent();
        $event->setCurrent($note);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveModel($note);

        $event = new PostOrderNoteSaveEvent();
        $event->setCurrent($note);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

    public function getOrderFromPayment(Payment $payment): ?Order
    {
        return $this->hydrator->hydrate(Order::class, $this->repository->getOrderFromPayment($payment));
    }

    public function getOrderFlagTypeById(int $id): ?OrderFlagType
    {
        return $this->hydrator->lookupRecord(OrderFlagType::class, $id);
    }

    public function getOrderFlagTypeByName(string $name): ?OrderFlagType
    {
        return $this->hydrator->hydrate(OrderFlagType::class, $this->repository->getOrderFlagTypeByName($name));
    }

    /**
     * @return OrderFlagType[]
     */
    public function getAllOrderFlagTypes(): array
    {
        return $this->hydrator->lookupAll(OrderFlagType::class);
    }
}
