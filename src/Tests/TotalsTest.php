<?php

namespace Pantono\Cart\Tests;

use Pantono\Cart\Model\{Cart,
    CartCode,
    CartItem,
    DeliveryCost,
    DeliverySpeed,
    Order,
    OrderItemStatus,
    OrderLineItem,
    OrderLineItemType,
    OrderStatus
};
use Pantono\Cart\Orders;
use Pantono\Cart\Repository\ShoppingCartRepository;
use Pantono\Cart\ShoppingCart;
use Pantono\Customers\Customers;
use Pantono\Hydrator\Hydrator;
use Pantono\Locations\Model\{Country, Location};
use Pantono\Products\Model\{Discount, DiscountBase, DiscountCode, Product, ProductVatRate, ProductVersion};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TotalsTest extends TestCase
{
    public static function totalsCases(): array
    {
        return [
            'reported example' => [[[800, 1, 0.2]], 89, 0.2, 'amount', 80, 80, 161.8, 970.8],
            'quantity' => [[[800, 2, 0.2]], 89, 0.2, 'amount', 80, 80, 321.8, 1930.8],
            'without delivery' => [[[800, 1, 0.2]], null, null, 'amount', 80, 80, 144, 864],
            'no discount' => [[[800, 1, 0.2]], 89, 0.2, null, 0, 0, 177.8, 1066.8],
            'mixed VAT fixed discount' => [[[100, 1, 0.2], [100, 1, 0]], 10, 0.05, 'amount', 20, 20, 18.5, 208.5],
            'mixed VAT percentage discount' => [[[100, 1, 0.2], [100, 1, 0]], 10, 0.05, 'percentage', 10, 20, 18.5, 208.5],
            'mixed VAT weighted by quantity' => [[[100, 2, 0.2], [100, 1, 0]], null, null, 'amount', 30, 30, 36, 306],
            'free delivery at different rate' => [[[100, 1, 0.2]], 10, 0.05, 'delivery', 0, 10, 20, 120],
            'delivery without VAT rate' => [[[100, 1, 0.2]], 10, null, 'delivery', 0, 10, 20, 120],
            'zero rate' => [[[100, 1, 0]], 10, 0, 'amount', 20, 20, 0, 90],
            'fractional allocation' => [[[1, 1, 0.2], [1, 1, 0.05], [1, 1, 0]], null, null, 'amount', 1, 1, 0.17, 2.17],
            'fractional VAT' => [[[19.99, 3, 0.2]], 4.99, 0.2, 'percentage', 10, 6, 11.79, 70.75],
        ];
    }

    #[DataProvider('totalsCases')]
    public function testCartAndConvertedOrderTotals(array $products, ?float $delivery, ?float $deliveryRate, ?string $discountType, float $amount, float $expectedDiscount, float $expectedVat, float $expectedTotal): void
    {
        $cart = $this->cart($products, $delivery, $deliveryRate);
        if ($discountType !== null) {
            $cart->setCodes([$this->discount($discountType, $amount)]);
        }
        $order = $this->convert($cart);

        self::assertEqualsWithDelta($delivery === null ? 0 : $delivery * (1 + ($deliveryRate ?? 0)), $cart->getShippingCostGross() ?? 0, 0.000001);
        self::assertEqualsWithDelta($expectedDiscount, $cart->getDiscount(), 0.000001);
        self::assertEqualsWithDelta($expectedDiscount, $order->getDiscountTotal(), 0.000001);
        self::assertEqualsWithDelta($expectedVat, $cart->getVat(), 0.000001);
        self::assertEqualsWithDelta($expectedVat, $order->getVatTotal(), 0.000001);
        self::assertEqualsWithDelta($expectedTotal, $cart->getGrandTotal(), 0.000001);
        self::assertEqualsWithDelta($expectedTotal, $order->getGrandTotal(), 0.000001);
        self::assertEqualsWithDelta($expectedTotal, round(array_sum(array_map(fn(OrderLineItem $line) => $line->getLineTotalIncVat(), $order->getItems())), 2), 0.000001);
    }

    public function testStackedProductAndDeliveryDiscounts(): void
    {
        $cart = $this->cart([[100, 1, 0.2], [100, 1, 0]], 10, 0.05);
        $cart->setCodes([$this->discount('percentage', 10), $this->discount('delivery', 0)]);
        $order = $this->convert($cart);
        self::assertSame(30.0, $cart->getDiscount());
        self::assertSame(18.0, $cart->getVat());
        self::assertSame(198.0, $cart->getGrandTotal());
        self::assertSame($cart->getGrandTotal(), $order->getGrandTotal());
        self::assertSame($cart->getVat(), $order->getVatTotal());
    }

    public function testMinimumSpendAndDiscountDisplayShape(): void
    {
        $cart = $this->cart([[100, 1, 0.2]], null, null);
        $code = $this->discount('amount', 10);
        $cart->setCodes([$code]);
        foreach ([null, 0, 100] as $minimum) {
            $code->getCode()->getDiscount()->setMinSpend($minimum);
            self::assertSame([['name' => 'Test discount', 'amount' => 10.0]], $cart->getDiscountLineItems());
        }
        $code->getCode()->getDiscount()->setMinSpend(101);
        self::assertSame([], $cart->getDiscountLineItems());
        self::assertSame(120.0, $cart->getGrandTotal());
    }

    public static function lineCases(): array
    {
        return [
            'taxed product' => [800, 1, 0.2, false, 800, 160, 960, 960],
            'taxed discount' => [80, 2, 0.2, true, -160, -32, -96, -192],
            'untaxed quantity' => [80, 2, null, false, 160, 0, 80, 160],
            'untaxed discount quantity' => [80, 2, null, true, -160, 0, -80, -160],
            'zero-rated quantity' => [80, 2, 0, false, 160, 0, 80, 160],
            'already negative discount' => [-80, 2, 0.2, true, -160, -32, -96, -192],
        ];
    }

    #[DataProvider('lineCases')]
    public function testLineTotals(float $price, int $quantity, ?float $rate, bool $discount, float $net, float $vat, float $unitGross, float $gross): void
    {
        $line = new OrderLineItem();
        $line->setType($this->lineType($discount ? Orders::LINE_TYPE_DISCOUNT : Orders::LINE_TYPE_PRODUCT));
        $line->setPrice($price);
        $line->setQuantity($quantity);
        $line->setVatRate($rate === null ? null : $this->vatRate($rate));
        self::assertSame($net, $line->getLineTotal());
        self::assertSame($vat, $line->getLineVatTotal());
        self::assertSame($unitGross, $line->getItemPriceIncVat());
        self::assertSame($gross, $line->getLineTotalIncVat());
        $order = new Order();
        $order->addItem($line);
        self::assertSame($vat, $order->getVatTotal());
        self::assertSame($gross, $order->getGrandTotal());
    }

    private function cart(array $products, ?float $delivery, ?float $deliveryRate): Cart
    {
        $cart = new Cart();
        $cart->setForename('Test');
        $cart->setSurname('Customer');
        $cart->setTelephone('01234567890');
        $cart->setEmail('test@example.com');
        $country = new Country();
        $country->setId(1);
        $location = new Location();
        $location->setCountry($country);
        $cart->setShippingLocation($location);
        $cart->setBillingLocation($location);
        $speed = new DeliverySpeed();
        $speed->setId(1);
        $cart->setDeliverySpeed($speed);
        if ($delivery !== null) {
            $cost = new DeliveryCost();
            $cost->setCost($delivery);
            $cost->setVatRate($deliveryRate === null ? null : $this->vatRate($deliveryRate));
            $cost->setCountry($country);
            $cost->setSpeed($speed);
            $cost->setMinWeight(0);
            $cost->setMaxWeight(1000);
            $speed->setCosts([$cost]);
        }
        $items = [];
        foreach ($products as [$price, $quantity, $rate]) {
            $version = new ProductVersion();
            $version->setPrice($price);
            $version->setVatRate($this->vatRate($rate));
            $version->setWeight(1);
            $version->setDeliveryPrice(null);
            $product = new Product();
            $product->setPublishedDraft($version);
            $item = new CartItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $items[] = $item;
        }
        $cart->setItems($items);
        return $cart;
    }

    private function discount(string $type, float $amount): CartCode
    {
        $base = new DiscountBase();
        $base->setPercentage($type === 'percentage');
        $base->setAmount($type === 'amount');
        $base->setFreeDelivery($type === 'delivery');
        $discount = new Discount();
        $discount->setBase($base);
        $discount->setName('Test discount');
        $discount->setAmount($amount);
        $code = new DiscountCode();
        $code->setCode('TEST');
        $code->setDiscount($discount);
        $cartCode = new CartCode();
        $cartCode->setCode($code);
        return $cartCode;
    }

    private function vatRate(float $rate): ProductVatRate
    {
        $vat = new ProductVatRate();
        $vat->setId((int)($rate * 100) + 1);
        $vat->setRate($rate);
        return $vat;
    }

    private function lineType(int $id): OrderLineItemType
    {
        $type = new OrderLineItemType();
        $type->setId($id);
        $type->setProduct($id === Orders::LINE_TYPE_PRODUCT);
        $type->setDelivery($id === Orders::LINE_TYPE_DELIVERY);
        $type->setDiscount($id === Orders::LINE_TYPE_DISCOUNT);
        return $type;
    }

    private function convert(Cart $cart): Order
    {
        $hydrator = $this->createStub(Hydrator::class);
        $hydrator->method('lookupRecord')->willReturnCallback(fn(string $class, int $id) => match ($class) {
            OrderStatus::class => new OrderStatus(),
            OrderItemStatus::class => new OrderItemStatus(),
            OrderLineItemType::class => $this->lineType($id),
        });
        $service = new ShoppingCart(
            $this->createStub(ShoppingCartRepository::class),
            $hydrator,
            new EventDispatcher(),
            $this->createStub(Customers::class)
        );
        return $service->convertCartToOrder($cart);
    }
}
