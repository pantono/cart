<?php

namespace Pantono\Cart\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pantono\Cart\Event\PreCartSaveEvent;

class CheckCartSpeed implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PreCartSaveEvent::class => [
                ['checkSpeed', 255]
            ]
        ];
    }

    public function checkSpeed(PreCartSaveEvent $event): void
    {
        $event->getCurrent()->checkSpeed();;
    }
}
