<?php

namespace Pantono\Cart\Repository;

use Pantono\Database\Repository\DefaultRepository;
use Pantono\Cart\Filter\OrderFilter;
use Pantono\Cart\Model\Order;

class OrdersRepository extends DefaultRepository
{

    /**
     * @return array<int,mixed>
     */
    public function getOrdersByFilter(OrderFilter $filter): array
    {
        $orderTable = $this->appendTablePrefix('order');
        $lineItemTable = $this->appendTablePrefix('order_line_item');
        $productVersionTable = $this->appendTablePrefix('product_version');
        $productTable = $this->appendTablePrefix('product');

        $select = $this->getDb()->select('o.*')->from($orderTable, 'o')
            ->innerJoin('o', $lineItemTable, 'i', 'o.id = i.order_id')
            ->innerJoin('i', $productVersionTable, 'pv', 'i.product_version_id = pv.id')
            ->innerJoin('pv', $productTable, 'p', 'pv.product_id = p.id')
            ->addOrderBy($filter->getOrder(), $filter->getDirection())
            ->groupBy('o.id');


        if ($filter->getCustomer() !== null) {
            $select->where('o.customer_id=:customer_id')
                ->setParameter('customer_id', $filter->getCustomer()->getId());
        }
        if ($filter->getCompany() !== null) {
            $select->where('pv.company_id=:company_id')
                ->setParameter('company_id', $filter->getCompany()->getId());
        }
        if ($filter->getDatePlacedStart() !== null) {
            $select->where('o.date_created >= :date_placed_start')
                ->setParameter('date_placed_start', $filter->getDatePlacedStart()->format('Y-m-d H:i:s'));
        }
        if ($filter->getDatePlacedEnd() !== null) {
            $select->where('o.date_created <= :date_placed_end')
                ->setParameter('date_placed_end', $filter->getDatePlacedEnd()->format('Y-m-d H:i:s'));
        }
        if ($filter->getStatus() !== null) {
            $select->where('o.status_id=:status_id')
                ->setParameter('status_id', $filter->getStatus()->getId());
        }
        if ($filter->getFolder() !== null) {
            $select->where('o.folder_id=:folder_id')
                ->setParameter('folder_id', $filter->getFolder()->getId());
        }
        if ($filter->getName() !== null) {
            $select->where('o.name LIKE :name')
                ->setParameter('name', '%' . $filter->getName() . '%');
        }
        if ($filter->getOrderRef() !== null) {
            $select->where('o.order_ref LIKE :order_ref')
                ->setParameter('order_ref', '%' . $filter->getOrderRef() . '%');
        }
        if ($filter->getProductSearch() !== null) {
            $select->where('(pv.title LIKE :product_search or p.code like :product_search')
                ->setParameter('product_search', '%' . $filter->getProductSearch() . '%');
        }

        $this->applyCountAndLimit($select, $filter);

        return $this->getDb()->fetchAll($select);
    }

    /**
     * @return array<int,mixed>
     */
    public function getPaymentsForOrder(Order $order): array
    {
        $select = $this->getDb()->select('p.*')->from($this->appendTablePrefix('order_payment'), 'op')
            ->innerJoin('op', 'payment', 'p', 'op.payment_id = p.id')
            ->where('op..order_id=:order_id')
            ->setParameter('order_id', $order->getId());

        return $this->getDb()->fetchAll($select);
    }

    public function saveOrder(Order $order): void
    {
        $orderTable = $this->appendTablePrefix('order');
        $id = $this->insertOrUpdate($orderTable, 'id', $order->getId(), $order->getAllData());
        if ($id) {
            $order->setId($id);
        }

        $itemIds = [];
        foreach ($order->getItems() as $item) {
            $item->setOrderId($order->getId());
            $itemId = $this->insertOrUpdate($this->appendTablePrefix('order_item'), 'id', $item->getId(), $item->getAllData());
            if ($itemId) {
                $item->setId($itemId);
            }
            $itemIds[] = $item->getId();
        }
        $params = ['order_id=?' => $order->getId()];
        if (!empty($itemIds)) {
            $params['id NOT IN (?)'] = $itemIds;
        }
        $this->getDb()->delete($this->appendTablePrefix('order_item'), $params);

        $this->getDb()->delete($this->appendTablePrefix('order_payment'), ['order_id=?' => $order->getId()]);
        foreach ($order->getPayments() as $payment) {
            $this->getDb()->insert($this->appendTablePrefix('order_payment'), ['order_id' => $order->getId(), 'payment_id' => $payment->getId()]);
        }
    }
}
