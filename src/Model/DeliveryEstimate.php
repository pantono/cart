<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Contracts\Attributes\Database\OneToOne;

#[DatabaseTable('delivery_estimate')]
class DeliveryEstimate
{
    private ?int $id = null;
    #[OneToOne(targetModel: DeliverySpeed::class)]
    private ?DeliverySpeed $deliverySpeed = null;
    #[OneToOne(targetModel: DeliveryType::class)]
    private ?DeliveryType $deliveryType = null;
    private int $orderDay;
    private int $deliveryTime;
    private \DateTimeInterface $timeCutoff;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDeliverySpeed(): ?DeliverySpeed
    {
        return $this->deliverySpeed;
    }

    public function setDeliverySpeed(?DeliverySpeed $deliverySpeed): void
    {
        $this->deliverySpeed = $deliverySpeed;
    }

    public function getDeliveryType(): ?DeliveryType
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(?DeliveryType $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    public function getOrderDay(): int
    {
        return $this->orderDay;
    }

    public function setOrderDay(int $orderDay): void
    {
        $this->orderDay = $orderDay;
    }

    public function getDeliveryTime(): int
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(int $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getTimeCutoff(): \DateTimeInterface
    {
        return $this->timeCutoff;
    }

    public function setTimeCutoff(\DateTimeInterface $timeCutoff): void
    {
        $this->timeCutoff = $timeCutoff;
    }
}
