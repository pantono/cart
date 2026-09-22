<?php

namespace Pantono\Cart\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pantono\Payments\Event\PostPaymentSaveEvent;
use Pantono\Cart\Orders;
use Pantono\Payments\Payments;

class ApplyRefundToOrder implements EventSubscriberInterface
{
    private Orders $orders;
    private Payments $payments;

    public function __construct(Orders $orders, Payments $payments)
    {
        $this->orders = $orders;
        $this->payments = $payments;
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
