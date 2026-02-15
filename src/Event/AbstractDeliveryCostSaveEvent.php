<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\DeliveryCost;

class AbstractDeliveryCostSaveEvent extends Event
{
    private DeliveryCost $current;
    private ?DeliveryCost $previous = null;

    public function getCurrent(): DeliveryCost
    {
        return $this->current;
    }

    public function setCurrent(DeliveryCost $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?DeliveryCost
    {
        return $this->previous;
    }

    public function setPrevious(?DeliveryCost $previous): void
    {
        $this->previous = $previous;
    }
}
