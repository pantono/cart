<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\Cart;

class AbstractCartSaveEvent extends Event
{
    private Cart $current;
    private ?Cart $previous = null;

    public function getCurrent(): Cart
    {
        return $this->current;
    }

    public function setCurrent(Cart $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?Cart
    {
        return $this->previous;
    }

    public function setPrevious(?Cart $previous): void
    {
        $this->previous = $previous;
    }
}
