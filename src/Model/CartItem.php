<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Products\Model\Product;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Database\Traits\SavableModel;
use Pantono\Contracts\Attributes\Locator;
use Pantono\Products\Model\SpecialOffer;
use Pantono\Cart\ShoppingCart;
use Pantono\Contracts\Attributes\Lazy;

#[DatabaseTable('cart_item')]
class CartItem implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    private int $cartId;
    #[OneToOne(targetModel: Product::class), FieldName('product_id')]
    private Product $product;
    private int $quantity;
    private \DateTimeInterface $dateAdded;
    /**
     * @var SpecialOffer[]
     */
    #[Locator(className: ShoppingCart::class, methodName: 'getActiveSpecialOffersForCartItem'), FieldName('$this'), Lazy]
    private array $specialOffers = [];

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

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
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

    public function getDateAdded(): \DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return SpecialOffer[]
     */
    public function getSpecialOffers(): array
    {
        return $this->specialOffers;
    }

    public function setSpecialOffers(array $specialOffers): void
    {
        $this->specialOffers = $specialOffers;
    }
}
