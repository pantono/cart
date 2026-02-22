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

        $select = $this->getDb()->select()->from($orderTable)
            ->joinInner($lineItemTable, $lineItemTable . '.order_id=' . $orderTable . '.id', [])
            ->joinInner($productVersionTable, $productVersionTable . '.id=' . $lineItemTable . '.product_version_id', [])
            ->joinInner($productTable, $productTable . '.id=' . $productVersionTable . '.product_id', [])
            ->order($filter->getOrder() . ' ' . $filter->getDirection())
            ->group($orderTable . '.id');

        if ($filter->getCustomer() !== null) {
            $select->where($orderTable . '.customer_id=?', $filter->getCustomer()->getId());
        }
        if ($filter->getCompany() !== null) {
            $select->where($productVersionTable . '.company_id=?', $filter->getCompany()->getId());
        }
        if ($filter->getDatePlacedStart() !== null) {
            $select->where($orderTable . '.date_created >= ?', $filter->getDatePlacedStart()->format('Y-m-d H:i:s'));
        }
        if ($filter->getDatePlacedEnd() !== null) {
            $select->where($orderTable . '.date_created <= ?', $filter->getDatePlacedEnd()->format('Y-m-d H:i:s'));
        }
        if ($filter->getStatus() !== null) {
            $select->where($orderTable . '.status_id=?', $filter->getStatus()->getId());
        }
        if ($filter->getFolder() !== null) {
            $select->where($orderTable . '.folder_id=?', $filter->getFolder()->getId());
        }
        if ($filter->getName() !== null) {
            $select->where($orderTable . '.name LIKE ?', '%' . $filter->getName() . '%');
        }
        if ($filter->getOrderRef() !== null) {
            $select->where($orderTable . '.order_ref LIKE ?', '%' . $filter->getOrderRef() . '%');
        }
        if ($filter->getProductSearch() !== null) {
            $select->where('(' . $productVersionTable . '.title LIKE ?', '%' . $filter->getProductSearch() . '%')
                ->orWhere($productTable . '.code LIKE ?)', '%' . $filter->getProductSearch() . '%');
        }

        $filter->setTotalResults($this->getCount($select));
        $select->limitPage($filter->getPage(), $filter->getPerPage());

        return $this->getDb()->fetchAll($select);
    }

    /**
     * @return array<int,mixed>
     */
    public function getPaymentsForOrder(Order $order): array
    {
        $select = $this->getDb()->select()->from($this->appendTablePrefix('order_payment'), [])
            ->joinInner($this->appendTablePrefix('payment'), $this->appendTablePrefix('order_payment') . '.payment_id=' . $this->appendTablePrefix('payment') . '.id')
            ->where($this->appendTablePrefix('order_payment') . '.order_id=?', $order->getId());

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
