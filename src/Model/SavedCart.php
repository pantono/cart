<?php

namespace Pantono\Cart\Model;

use Pantono\Contracts\Attributes\DatabaseTable;
use Pantono\Authentication\Model\User;
use Pantono\Contracts\Attributes\Database\OneToOne;
use Pantono\Contracts\Attributes\FieldName;
use Pantono\Contracts\Attributes\Database\OneToMany;
use Pantono\Database\Traits\SavableModel;
use Pantono\Contracts\Application\Interfaces\SavableInterface;
use Pantono\Products\Model\Product;

#[DatabaseTable('cart_saved')]
class SavedCart implements SavableInterface
{
    use SavableModel;

    private ?int $id = null;
    #[OneToOne(targetModel: User::class), FieldName('user_id')]
    private ?User $user = null;
    private \DateTimeInterface $dateCreated;
    private ?string $name = null;
    private bool $deleted = false;
    /**
     * @var SavedCartItem[]
     */
    #[OneToMany(targetModel: SavedCartItem::class, mappedBy: 'saved_cart_id')]
    private array $items = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return SavedCartItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function addItem(SavedCartItem $item): void
    {
        $this->items[] = $item->getId();
    }

    public function addProduct(Product $product, int $quantity): void
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($quantity);
                return;
            }
        }
        $cartItem = new SavedCartItem();
        $cartItem->setQuantity($quantity);
        $cartItem->setProduct($product);
        $this->items[] = $cartItem;
    }
}
