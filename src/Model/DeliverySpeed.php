<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Contracts\Attributes\Database\OneToMany;

#[DatabaseTable('delivery_speed')]
class DeliverySpeed
{
    private ?int $id = null;
    private string $name;
    private bool $live;
    #[OneToMany(targetModel: DeliveryCost::class, mappedBy: 'speed_id')]
    private array $costs = [];

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

    public function getCosts(): array
    {
        return $this->costs;
    }

    public function setCosts(array $costs): void
    {
        $this->costs = $costs;
    }
}
