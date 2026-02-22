<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;

#[DatabaseTable('order_line_item_type')]
class OrderLineItemType
{
    private ?int $id = null;
    private string $name;
    private bool $delivery;
    private bool $discount;
    private bool $product;

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

    public function isDelivery(): bool
    {
        return $this->delivery;
    }

    public function setDelivery(bool $delivery): void
    {
        $this->delivery = $delivery;
    }

    public function isDiscount(): bool
    {
        return $this->discount;
    }

    public function setDiscount(bool $discount): void
    {
        $this->discount = $discount;
    }

    public function isProduct(): bool
    {
        return $this->product;
    }

    public function setProduct(bool $product): void
    {
        $this->product = $product;
    }
}
