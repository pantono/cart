<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;

#[DatabaseTable('delivery_speed')]
class DeliverySpeed
{
    private ?int $id = null;
    private string $name;
    private bool $live;

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

    public function isLive(): bool
    {
        return $this->live;
    }

    public function setLive(bool $live): void
    {
        $this->live = $live;
    }
}
