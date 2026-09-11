<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\OrderNote;

abstract class AbstractOrderNoteSaveEvent extends Event
{
    private OrderNote $current;
    private ?OrderNote $previous = null;

    public function getCurrent(): OrderNote
    {
        return $this->current;
    }

    public function setCurrent(OrderNote $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?OrderNote
    {
        return $this->previous;
    }

    public function setPrevious(?OrderNote $previous): void
    {
        $this->previous = $previous;
    }
}
