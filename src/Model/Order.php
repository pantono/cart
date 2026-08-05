<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Locations\Model\Location;
use Pantono\Customers\Model\Customer;
use Pantono\Contracts\Attributes\Database\OneToMany;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;
use Pantono\Contracts\Attributes\Locator;
use Pantono\Payments\Model\Payment;
use Pantono\Cart\Orders;
use Pantono\Contracts\Attributes\DatabaseTable;

#[DatabaseTable('order')]
class Order implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private \DateTimeInterface $dateCreated;
    private \DateTimeInterface $dateUpdated;
    private ?string $reference = null;
    #[OneToOne(targetModel: OrderStatus::class), FieldName('status_id')]
    private ?OrderStatus $status = null;
    #[OneToOne(targetModel: Location::class), FieldName('delivery_location_id')]
    private ?Location $deliveryLocation = null;
    #[OneToOne(targetModel: Location::class), FieldName('billing_location_id')]
    private ?Location $billingLocation = null;
    #[OneToOne(targetModel: DeliverySpeed::class), FieldName('delivery_speed_id')]
    private ?DeliverySpeed $deliverySpeed = null;
    #[OneToOne(targetModel: Customer::class), FieldName('customer_id')]
    private ?Customer $customer = null;
    #[OneToOne(targetModel: OrderFolder::class), FieldName('order_folder_id')]
    private ?OrderFolder $folder = null;
    private string $forename;
    private string $surname;
    private string $email;
    private string $telephone;
    /**
     * @var OrderLineItem[]
     */
    #[OneToMany(targetModel: OrderLineItem::class, mappedBy: 'order_id')]
    private array $items = [];
    /**
     * @var Payment[]
     */
    #[Locator(methodName: 'getPaymentsForOrder', className: Orders::class), FieldName('$this')]
    private array $payments = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    public function getDateUpdated(): \DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTimeInterface $dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getDeliveryLocation(): ?Location
    {
        return $this->deliveryLocation;
    }

    public function setDeliveryLocation(?Location $deliveryLocation): void
    {
        $this->deliveryLocation = $deliveryLocation;
    }

    public function getBillingLocation(): ?Location
    {
        return $this->billingLocation;
    }

    public function setBillingLocation(?Location $billingLocation): void
    {
        $this->billingLocation = $billingLocation;
    }

    public function getDeliverySpeed(): ?DeliverySpeed
    {
        return $this->deliverySpeed;
    }

    public function setDeliverySpeed(?DeliverySpeed $deliverySpeed): void
    {
        $this->deliverySpeed = $deliverySpeed;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getFolder(): ?OrderFolder
    {
        return $this->folder;
    }

    public function setFolder(?OrderFolder $folder): void
    {
        $this->folder = $folder;
    }

    public function getForename(): string
    {
        return $this->forename;
    }

    public function setForename(string $forename): void
    {
        $this->forename = $forename;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return OrderLineItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @return OrderLineItem[]
     */
    public function getProductItems(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($item->getType()->isProduct()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @param int $id
     * @return OrderLineItem[]
     */
    public function getDeliveryItems(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($item->getType()->isDelivery()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @return OrderLineItem[]
     */
    public function getDiscountItems(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($item->getType()->isDiscount()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    public function getSubTotal(): float
    {
        $total = 0;
        foreach ($this->getProductItems() as $item) {
            $total += $item->getQuantity() * $item->getPrice();
        }
        return $total;
    }

    public function getDeliveryTotal(): float
    {
        $total = 0;
        foreach ($this->getDeliveryItems() as $item) {
            $total += $item->getQuantity() * $item->getPrice();
        }
        return $total;
    }

    public function getDiscountTotal(): float
    {
        $total = 0;
        foreach ($this->getDiscountItems() as $item) {
            $total += $item->getQuantity() * $item->getPrice();
        }
        return $total;
    }

    public function getGrandTotal(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item->getQuantity() * $item->getPrice();
        }
        return $total;
    }

    public function getVatTotal(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += ($item->getQuantity() * $item->getPrice()) * $item->getVatRate()->getRate();
        }
        return $total;
    }


    public function addItem(OrderLineItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    public function setPayments(array $payments): void
    {
        $this->payments = $payments;
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments[] = $payment;
    }

    public function getTotalDeliveryCost(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getType()->isDelivery()) {
                $total += $item->getQuantity() * $item->getPrice();
            }
        }
        return $total;
    }
}
