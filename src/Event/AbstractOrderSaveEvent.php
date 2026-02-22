<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\Order;

abstract class AbstractOrderSaveEvent extends Event
{
    private Order $current;
    private ?Order $previous = null;

    public function getCurrent(): Order
    {
        return $this->current;
    }

    public function setCurrent(Order $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?Order
    {
        return $this->previous;
    }

    public function setPrevious(?Order $previous): void
    {
        $this->previous = $previous;
    }
}
