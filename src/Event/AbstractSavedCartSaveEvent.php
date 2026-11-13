<?php

namespace Pantono\Cart\Event;

use Pantono\Cart\Model\SavedCart;

abstract class AbstractSavedCartSaveEvent
{
    private SavedCart $current;
    private ?SavedCart $previous = null;

    public function getCurrent(): SavedCart
    {
        return $this->current;
    }

    public function setCurrent(SavedCart $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?SavedCart
    {
        return $this->previous;
    }

    public function setPrevious(?SavedCart $previous): void
    {
        $this->previous = $previous;
    }
}
