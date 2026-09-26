<?php

namespace Pantono\Cart\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pantono\Payments\Event\PostPaymentSaveEvent;
use Pantono\Cart\Orders;

class ApplyRefundToOrder implements EventSubscriberInterface
{
    private Orders $orders;

    public function __construct(Orders $orders)
    {
        $this->orders = $orders;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostPaymentSaveEvent::class => [
                ['checkRefund', 255]
            ]
        ];
    }

    public function checkRefund(PostPaymentSaveEvent $event): void
    {
        $payment = $event->getCurrent();
        if (!$event->getPrevious() && $payment->getParentPayment()) {
            $parent = $payment->getParentPayment();
            $order = $this->orders->getOrderFromPayment($parent);
            if ($order) {
                $order->addPayment($payment);
                $this->orders->saveOrder($order);
            }
        }
    }
}
