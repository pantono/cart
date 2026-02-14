<?php

namespace Pantono\Cart\Model;

use Pantono\Payments\Model\Payment;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;

class CartPayment implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private int $cartId;
    #[OneToOne(Payment::class)]
    private ?Payment $payment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): void
    {
        $this->payment = $payment;
    }
}
