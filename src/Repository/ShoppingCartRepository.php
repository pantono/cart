<?php

namespace Pantono\Cart\Repository;

use Pantono\Database\Repository\DefaultRepository;
use Pantono\Cart\Model\Cart;

class ShoppingCartRepository extends DefaultRepository
{
    /**
     * @return array<int, mixed>|null
     */
    public function getActiveCartForSession(string $sessionId): ?array
    {
        $select = $this->getDb()->select()->from('cart')
            ->where('session_id=?', $sessionId)
            ->where('order_id is null');

        return $this->getDb()->fetchRow($select);
    }

    public function saveCart(Cart $cart): void
    {
        $this->saveModel($cart);
        $itemIds = [];
        foreach ($cart->getItems() as $item) {
            $item->setCartId($cart->getId());
            $this->saveModel($item);
            $itemIds[] = $item->getId();
        }
        $params = ['cart_id=?' => $cart->getId()];
        if (!empty($itemIds)) {
            $params['id not in (?)'] = $itemIds;
        }
        $this->getDb()->delete($this->appendTablePrefix('cart_item'), $params);

        $this->getDb()->delete($this->appendTablePrefix('cart_payment'), ['cart_id=?' => $cart->getId()]);
        foreach ($cart->getPayments() as $payment) {
            $this->getDb()->insert($this->appendTablePrefix('cart_payment'), ['cart_id' => $cart->getId(), 'payment_id' => $payment->getId()]);
        }

        $this->getDb()->delete($this->appendTablePrefix('cart_code'), ['cart_id=?' => $cart->getId()]);
        foreach ($cart->getPayments() as $code) {
            $this->getDb()->insert($this->appendTablePrefix('cart_code'), ['cart_id' => $cart->getId(), 'code_id' => $code->getId()]);
        }
    }

    /**
     * @return array<int, mixed>
     */
    public function getActiveSpecialOffersForProduct(int $productId, ?\DateTimeInterface $date = null): array
    {
        $select = $this->getDb()->select()->from($this->appendTablePrefix('special_offer_product'), [])
            ->joinInner($this->appendTablePrefix('special_offer'), $this->appendTablePrefix('special_offer_product') . '.offer_id=' . $this->appendTablePrefix('special_offers') . '.id')
            ->where($this->appendTablePrefix('special_offer') . '.start_date <= ?', $date->format('Y-m-d H:i:s'))
            ->where($this->appendTablePrefix('special_offer') . '.end_date >= ?', $date->format('Y-m-d H:i:s'))
            ->where($this->appendTablePrefix('special_offer_product') . '.product_id=?', $productId);

        return $this->getDb()->fetchAll($select);
    }
}
