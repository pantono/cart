<?php

namespace Pantono\Cart\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pantono\Cart\Event\PostOrderSaveEvent;
use Pantono\Logger\AuditLogger;
use Pantono\Cart\Model\Order;

class PostOrderSaveTasks implements EventSubscriberInterface
{

    private AuditLogger $logger;

    public function __construct(AuditLogger $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostOrderSaveEvent::class => [
                ['addLog', -255]
            ]
        ];
    }

    public function addLog(PostOrderSaveEvent $event): void
    {
        $order = $event->getCurrent();
        if (!$event->getPrevious()) {
            $this->logger->addLogForModel(Order::class, (string)$order->getId(), 'Created new order');
            return;
        }
        $this->logger->autoLog($event->getCurrent(), $event->getPrevious());
    }
}
