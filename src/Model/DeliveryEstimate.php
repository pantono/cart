<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Locations\Model\Country;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;

#[DatabaseTable('delivery_estimate')]
class DeliveryEstimate implements SavableInterface
{
    use SavableModel;
    private ?int $id = null;
    #[OneToOne(targetModel: DeliverySpeed::class)]
    private ?DeliverySpeed $deliverySpeed = null;
    #[OneToOne(targetModel: Country::class), FieldName('country_id')]
    private ?Country $country = null;
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
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
