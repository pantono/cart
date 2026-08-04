<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Products\Model\Product;
use Pantono\Database\Traits\SavableModel;
use Pantono\Contracts\Application\Interfaces\SavableInterface;

#[DatabaseTable('cart_stock_reservation')]
class CartStockReservation implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    #[OneToOne(targetModel: Cart::class), FieldName('cart_id')]
    private ?Cart $cart = null;
    private \DateTimeInterface $dateReserved;
    private \DateTimeInterface $dateExpires;
    private int $quantity;
    #[OneToOne(targetModel: Product::class), FieldName('product_id')]
    private ?Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getDateReserved(): \DateTimeInterface
    {
        return $this->dateReserved;
    }

    public function setDateReserved(\DateTimeInterface $dateReserved): void
    {
        $this->dateReserved = $dateReserved;
    }

    public function getDateExpires(): \DateTimeInterface
    {
        return $this->dateExpires;
    }

    public function setDateExpires(\DateTimeInterface $dateExpires): void
    {
        $this->dateExpires = $dateExpires;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }
}
