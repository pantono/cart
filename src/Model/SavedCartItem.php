<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Products\Model\Product;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;

#[DatabaseTable('saved_cart_item')]
class SavedCartItem implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private int $savedCartId;
    #[OneToOne(targetModel: Product::class), FieldName('product_id')]
    private ?Product $product = null;
    private int $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSavedCartId(): int
    {
        return $this->savedCartId;
    }

    public function setSavedCartId(int $savedCartId): void
    {
        $this->savedCartId = $savedCartId;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
