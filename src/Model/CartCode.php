<?php

namespace Pantono\Cart\Model;

use Pantono\Products\Model\DiscountCode;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;

class CartCode
{
    private int $cartId;
    #[OneToOne(DiscountCode::class), FieldName('code_id')]
    private ?DiscountCode $code = null;
    private \DateTimeInterface $dateAdded;

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function getCode(): ?DiscountCode
    {
        return $this->code;
    }

    public function setCode(?DiscountCode $code): void
    {
        $this->code = $code;
    }

    public function getDateAdded(): \DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }
}
