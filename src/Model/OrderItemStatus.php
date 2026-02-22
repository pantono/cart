<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;

#[DatabaseTable('order_item_status')]
class OrderItemStatus
{
    private ?int $id = null;
    private string $name;
    private bool $dispatched;
    private bool $cancelled;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isDispatched(): bool
    {
        return $this->dispatched;
    }

    public function setDispatched(bool $dispatched): void
    {
        $this->dispatched = $dispatched;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }
}
