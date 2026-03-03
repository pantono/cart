<?php

namespace Pantono\Cart\Repository;

use Pantono\Database\Repository\DefaultRepository;
use Pantono\Cart\Model\Cart;

class ShoppingCartRepository extends DefaultRepository
{
    /**
     * @return array<mixed>|null
     */
    public function getActiveCartForSession(string $sessionId): ?array
    {
        $select = $this->getDb()->select('c.*')->from('cart', 'c')
            ->where('c.session_id=?')
            ->where('c.order_id is null')
            ->setParameter('session_id', $sessionId);

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
        if (!$date) {
            $date = new \DateTime();
        }
        $select = $this->getDb()->select('s.*')->from($this->appendTablePrefix('special_offer_product'), 'sop')
            ->innerJoin('sop', $this->appendTablePrefix('special_offer'), 'so', 'sop.special_offer_id=so.id')
            ->where('so.start_date <= :start_date')
            ->where('so.end_date >= :end_dat')
            ->where('sop.product_version_id=:product_version_id')
            ->setParameter('start_date', $date->format('Y-m-d H:i:s'))
            ->setParameter('end_date', $date->format('Y-m-d H:i:s'))
            ->setParameter('product_version_id', $productId);

        return $this->getDb()->fetchAll($select);
    }

    /**
     * @param Cart $cart
     * @return array<int, mixed>
     */
    public function getCodesForCart(Cart $cart): array
    {
        return $this->selectRowsByValues('cart_code', ['cart_id' => $cart->getId()]);
    }

    /**
     * @param Cart $cart
     * @return array<int, mixed>
     */
    public function getPaymentsForCart(Cart $cart): array
    {
        $select = $this->getDb()->select('p.*')->from($this->appendTablePrefix('cart_payment'), 'cp')
            ->innerJoin('cp', $this->appendTablePrefix('payment'), 'p', 'cp.payment_id=p.id')
            ->where('cp.cart_id=:id')
            ->setParameter('id', $cart->getId());

        return $this->getDb()->fetchAll($select);
    }

    /**
     * @return array<int, mixed>
     */
    public function getActiveSpeeds(): array
    {
        return $this->selectRowsByValues($this->appendTablePrefix('delivery_speed'), ['live' => 1]);
    }
}
