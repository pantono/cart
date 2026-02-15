<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Locations\Model\Country;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Products\Model\ProductVatRate;

#[DatabaseTable('delivery_cost')]
class DeliveryCost
{
    private ?int $id = null;
    private int $typeId;
    #[OneToOne(targetModel: DeliverySpeed::class), FieldName('speed_id')]
    private ?DeliverySpeed $speed;
    #[OneToOne(targetModel: Country::class), FieldName('country_id')]
    private ?Country $country = null;
    private float $cost;
    private float $minWeight;
    private float $maxWeight;
    private int $priority;
    #[OneToOne(targetModel: ProductVatRate::class), FieldName('vat_rate_id')]
    private ?ProductVatRate $vatRate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function getSpeed(): ?DeliverySpeed
    {
        return $this->speed;
    }

    public function setSpeed(?DeliverySpeed $speed): void
    {
        $this->speed = $speed;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    public function getMinWeight(): float
    {
        return $this->minWeight;
    }

    public function setMinWeight(float $minWeight): void
    {
        $this->minWeight = $minWeight;
    }

    public function getMaxWeight(): float
    {
        return $this->maxWeight;
    }

    public function setMaxWeight(float $maxWeight): void
    {
        $this->maxWeight = $maxWeight;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getVatRate(): ?ProductVatRate
    {
        return $this->vatRate;
    }

    public function setVatRate(?ProductVatRate $vatRate): void
    {
        $this->vatRate = $vatRate;
    }
}
