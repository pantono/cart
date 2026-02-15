<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\DeliverySpeed;

class AbstractDeliverySpeedSaveEvent extends Event
{
    private DeliverySpeed $current;
    private ?DeliverySpeed $previous = null;

    public function getCurrent(): DeliverySpeed
    {
        return $this->current;
    }

    public function setCurrent(DeliverySpeed $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?DeliverySpeed
    {
        return $this->previous;
    }

    public function setPrevious(?DeliverySpeed $previous): void
    {
        $this->previous = $previous;
    }
}
