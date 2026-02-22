<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;

#[DatabaseTable('order_status')]
class OrderStatus
{
    private ?int $id = null;
    private string $name;
    private bool $completed;
    private bool $cancelled;
    private bool $pending;

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

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }

    public function isPending(): bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }
}
