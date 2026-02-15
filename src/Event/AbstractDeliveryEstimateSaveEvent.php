<?php

namespace Pantono\Cart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pantono\Cart\Model\DeliveryEstimate;

class AbstractDeliveryEstimateSaveEvent extends Event
{
    private DeliveryEstimate $current;
    private ?DeliveryEstimate $previous = null;

    public function getCurrent(): DeliveryEstimate
    {
        return $this->current;
    }

    public function setCurrent(DeliveryEstimate $current): void
    {
        $this->current = $current;
    }

    public function getPrevious(): ?DeliveryEstimate
    {
        return $this->previous;
    }

    public function setPrevious(?DeliveryEstimate $previous): void
    {
        $this->previous = $previous;
    }
}
