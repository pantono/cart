<?php

namespace Pantono\Cart\Repository;

use Pantono\Database\Repository\DefaultRepository;
use Pantono\Cart\Model\SavedCart;
use Pantono\Cart\Filter\SavedCartFilter;

class SavedCartsRepository extends DefaultRepository
{

    public function saveCart(SavedCart $cart): void
    {
        $this->saveModel($cart);
        $ids = [];
        foreach ($cart->getItems() as $item) {
            $item->setSavedCartId($cart->getId());
            $this->saveModel($item);
            $ids[] = $item->getId();
        }
        $this->deleteNotIn($this->appendTablePrefix('cart_saved_item'), 'saved_cart_id', $cart->getId(), $ids);
    }

    /**
     * @param SavedCartFilter $filter
     * @return array<int,mixed>
     */
    public function getSavedCartsByFilter(SavedCartFilter $filter): array
    {
        $select = $this->getDb()->select('c.*')->from($this->pt('cart_saved'), 'c');

        if ($filter->getUser() !== null) {
            $select->andWhere('c.user_id=:user_id')
                ->setParameter('user_id', $filter->getUser()->getId());
        }

        $this->applyCountAndLimit($select, $filter);

        return $this->getDb()->fetchAll($select);
    }
}
