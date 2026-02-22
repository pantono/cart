<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Products\Model\ProductVersion;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Products\Model\ProductVatRate;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;

#[DatabaseTable('order_line_item')]
class OrderLineItem implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private int $orderId;
    #[OneToOne(targetModel: OrderLineItemType::class), FieldName('type_id')]
    private ?OrderLineItemType $type = null;
    #[OneToOne(targetModel: ProductVersion::class), FieldName('product_version_id')]
    private ?ProductVersion $productVersion = null;
    #[OneToOne(targetModel: ProductVersion::class), FieldName('product_version_id')]
    private ?OrderItemStatus $status = null;
    #[OneToOne(targetModel: ProductVatRate::class), FieldName('vat_rate_id')]
    private ?ProductVatRate $vatRate = null;
    private int $quantity;
    private float $price;
    private ?\DateTimeInterface $estimatedDeliveryDate = null;
    private ?\DateTimeInterface $dateDispatched = null;
    private ?string $trackingNumber = null;
    private ?string $trackingType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getType(): ?OrderLineItemType
    {
        return $this->type;
    }

    public function setType(?OrderLineItemType $type): void
    {
        $this->type = $type;
    }

    public function getProductVersion(): ?ProductVersion
    {
        return $this->productVersion;
    }

    public function setProductVersion(?ProductVersion $productVersion): void
    {
        $this->productVersion = $productVersion;
    }

    public function getStatus(): ?OrderItemStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderItemStatus $status): void
    {
        $this->status = $status;
    }

    public function getVatRate(): ?ProductVatRate
    {
        return $this->vatRate;
    }

    public function setVatRate(?ProductVatRate $vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getEstimatedDeliveryDate(): ?\DateTimeInterface
    {
        return $this->estimatedDeliveryDate;
    }

    public function setEstimatedDeliveryDate(?\DateTimeInterface $estimatedDeliveryDate): void
    {
        $this->estimatedDeliveryDate = $estimatedDeliveryDate;
    }

    public function getDateDispatched(): ?\DateTimeInterface
    {
        return $this->dateDispatched;
    }

    public function setDateDispatched(?\DateTimeInterface $dateDispatched): void
    {
        $this->dateDispatched = $dateDispatched;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    public function getTrackingType(): ?string
    {
        return $this->trackingType;
    }

    public function setTrackingType(?string $trackingType): void
    {
        $this->trackingType = $trackingType;
    }
}
