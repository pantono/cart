<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Authentication\Model\User;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\Database\OneToMany;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;
use Pantono\Contracts\Attributes\Locator;
use Pantono\Cart\ShoppingCart;
use Pantono\Products\Model\DiscountCode;
use Pantono\Payments\Model\Payment;
use Pantono\Products\Model\Product;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Locations\Model\Location;

#[DatabaseTable('cart')]
class Cart implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private string $sessionId;
    private \DateTimeInterface $dateCreated;
    private \DateTimeInterface $dateUpdated;
    #[OneToOne(targetModel: DeliverySpeed::class), FieldName('delivery_speed_id')]
    private ?DeliverySpeed $deliverySpeed = null;
    #[OneToOne(targetModel: User::class), FieldName('user_id')]
    private ?User $user = null;
    #[OneToOne(targetModel: Location::class), FieldName('shipping_location_id')]
    private ?Location $shippingLocation = null;
    #[OneToOne(targetModel: Location::class), FieldName('billing_location_id')]
    private ?Location $billingLocation = null;
    /**
     * @var CartItem[]
     */
    #[OneToMany(targetModel: CartItem::class, mappedBy: 'cart_id')]
    private array $items = [];
    /**
     * @var Payment[]
     */
    #[Locator(methodName: 'getPaymentsForCart', className: ShoppingCart::class)]
    private array $payments = [];
    /**
     * @var CartCode[]
     */
    #[Locator(methodName: 'getCodesForCart', className: ShoppingCart::class)]
    private array $codes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
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

    public function getDeliverySpeed(): ?DeliverySpeed
    {
        return $this->deliverySpeed;
    }

    public function setDeliverySpeed(?DeliverySpeed $deliverySpeed): void
    {
        $this->deliverySpeed = $deliverySpeed;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getShippingLocation(): ?Location
    {
        return $this->shippingLocation;
    }

    public function setShippingLocation(?Location $shippingLocation): void
    {
        $this->shippingLocation = $shippingLocation;
    }

    public function getBillingLocation(): ?Location
    {
        return $this->billingLocation;
    }

    public function setBillingLocation(?Location $billingLocation): void
    {
        $this->billingLocation = $billingLocation;
    }

    /**
     * @return CartItem[]
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

    /**
     * @return CartCode[]
     */
    public function getCodes(): array
    {
        return $this->codes;
    }

    public function setCodes(array $codes): void
    {
        $this->codes = $codes;
    }

    public function addCode(DiscountCode $code): bool
    {
        foreach ($this->getCodes() as $cartCode) {
            if ($code->getDiscount()->getId() === $cartCode->getCode()->getDiscount()->getId()) {
                return false;
            }
        }
        if ($code->getStartDate() && $code->getStartDate() > new \DateTime) {
            return false;
        }
        if ($code->getEndDate() && $code->getEndDate() < new \DateTime) {
            return false;
        }
        $codes = $this->getCodes();
        $cartCode = new CartCode();
        $cartCode->setCode($code);
        $cartCode->setDateAdded(new \DateTime);
        $codes[] = $cartCode;
        $this->setCodes($codes);
        return true;
    }

    public function addProduct(Product $product, int $quantity): ?CartItem
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                if ($item->getQuantity() + $quantity > $product->getStockHolding()) {
                    /**
                     * Don't update quantity if an item will be out of stock
                     */
                    return null;
                }
                $item->setQuantity($quantity);
                return $item;
            }
        }
        if ($product->getStockHolding() < $quantity) {
            return null;
        }
        $items = $this->getItems();
        $item = new CartItem();
        $item->setDateAdded(new \DateTime);
        $item->setQuantity($quantity);
        $item->setProduct($product);
        $items[] = $item;
        $this->setItems($items);
        return $item;
    }

    public function removeProduct(Product $product): void
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getId() !== $product->getId()) {
                $items[] = $item;
            }
        }
        $this->setItems($items);
    }

    public function getItemTotalNet(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getProduct()->getPublishedDraft()->getPrice() * $item->getQuantity();
        }
        return $total;
    }

    public function getItemTotalGross(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $version = $item->getProduct()->getPublishedDraft();
            $total += $version->getVatRate()->addToPrice($version->getPrice()) * $item->getQuantity();
        }
        return $total;
    }

    public function getShippingCostNet(): ?float
    {
        return $this->getDeliveryCost()?->getCost();
    }

    public function getShippingCostGross(): ?float
    {
        $cost = $this->getDeliveryCost();
        return $cost?->getVatRate()->addToPrice($cost->getCost());
    }

    public function getVat(): float
    {
        $vat = 0;
        foreach ($this->getItems() as $item) {
            $version = $item->getProduct()->getPublishedDraft();
            $vat += $version->getVatRate()->calculateVat($version->getPrice());
        }
        if ($this->getDeliveryCost()) {
            $vat += $this->getDeliveryCost()->getVatRate()->calculateVat($this->getDeliveryCost()->getCost());
        }
        return $vat;
    }

    public function getDeliveryCost(): ?DeliveryCost
    {
        if (!$this->getShippingLocation() || !$this->getShippingLocation()->getCountry()) {
            return null;
        }
        if (!$this->getDeliverySpeed()) {
            return null;
        }
        $country = $this->getShippingLocation()->getCountry();
        return array_find(
            $this->getDeliverySpeed()->getCosts(),
            fn($cost) => $cost->getCountry()->getId() === $country->getId() && $cost->getSpeed()->getId() === $this->getDeliverySpeed()->getId()
        );
    }

    public function getDiscount(): float
    {
        $discount = 0;
        foreach ($this->getDiscountLineItems() as $item) {
            $discount += $item['amount'];
        }
        return $discount;
    }

    /**
     * @return array<int, array{name: string, amount: float|null}>
     */
    public function getDiscountLineItems(): array
    {
        $lineItems = [];
        foreach ($this->getCodes() as $code) {
            $discount = $code->getCode()->getDiscount();
            if ($discount->getBase()->isFreeDelivery()) {
                $lineItems[] = ['name' => 'Free Delivery (' . $code->getCode()->getCode() . ')', 'amount' => $this->getShippingCostNet()];
            }
            if ($discount->getBase()->isPercentage()) {
                $net = $this->getItemTotalNet();
                if ($discount->getMinSpend() && $net >= $discount->getMinSpend()) {
                    $lineItems[] = ['name' => $discount->getName(), 'amount' => $net * ($discount->getAmount() / 100)];
                }
            }
            if ($discount->getBase()->isAmount()) {
                $net = $this->getItemTotalNet();
                if ($discount->getMinSpend() && $net >= $discount->getMinSpend()) {
                    $lineItems[] = ['name' => $discount->getName(), 'amount' => $discount->getAmount()];
                }
            }
        }
        return $lineItems;
    }

    public function getGrandTotal(): float
    {
        if ($this->getDeliveryCost()) {
            return $this->getItemTotalNet() + $this->getShippingCostNet() - $this->getDiscount() + $this->getVat();
        }
        return $this->getItemTotalGross() - $this->getDiscount();
    }
}
