<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class OrdersMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->table($this->addTablePrefix('order_status'))
            ->addColumn('name', 'string')
            ->addColumn('completed', 'boolean')
            ->addColumn('cancelled', 'boolean')
            ->addColumn('pending', 'boolean')
            ->create();

        $this->insertOnCreate($this->addTablePrefix('order_status'), [
            ['id' => 1, 'name' => 'Pending', 'completed' => 0, 'cancelled' => 0, 'pending' => 1],
            ['id' => 2, 'name' => 'Preparing', 'completed' => 0, 'cancelled' => 0, 'pending' => 1],
            ['id' => 3, 'name' => 'Dispatched', 'completed' => 1, 'cancelled' => 0, 'pending' => 0],
            ['id' => 4, 'name' => 'Cancelled', 'completed' => 0, 'cancelled' => 1, 'pending' => 0],
            ['id' => 5, 'name' => 'Partial Dispatch', 'completed' => 0, 'cancelled' => 0, 'pending' => 0],
        ]);

        $this->table($this->addTablePrefix('order_folder'))
            ->addColumn('name', 'string')
            ->create();

        $this->table($this->addTablePrefix('order'))
            ->addColumn('date_created', 'datetime')
            ->addColumn('date_updated', 'datetime')
            ->addColumn('reference', 'string')
            ->addLinkedColumn('status_id', $this->addTablePrefix('order_status'), 'id')
            ->addLinkedColumn('billing_location_id', $this->addTablePrefix('location'), 'id')
            ->addLinkedColumn('delivery_location_id', $this->addTablePrefix('location'), 'id')
            ->addLinkedColumn('delivery_speed_id', $this->addTablePrefix('delivery_speed'), 'id', ['null' => true])
            ->addLinkedColumn('customer_id', $this->addTablePrefix('customer'), 'id', ['null' => true])
            ->addLinkedColumn('order_folder_id', $this->addTablePrefix('order_folder'), 'id', ['null' => true])
            ->addColumn('forename', 'string')
            ->addColumn('surname', 'string')
            ->addColumn('email', 'string')
            ->addColumn('telephone', 'string')
            ->create();


        $this->table($this->addTablePrefix('order_item_status'))
            ->addColumn('name', 'string')
            ->addColumn('dispatched', 'boolean')
            ->addColumn('cancelled', 'boolean')
            ->create();

        $this->insertOnCreate($this->addTablePrefix('order_item_status'), [
            ['id' => 1, 'name' => 'Pending', 'dispatched' => 0, 'cancelled' => 0],
            ['id' => 2, 'name' => 'Dispatched', 'dispatched' => 0, 'cancelled' => 0],
        ]);

        $this->table($this->addTablePrefix('order_line_item_type'))
            ->addColumn('name', 'string')
            ->addColumn('delivery', 'boolean')
            ->addColumn('discount', 'boolean')
            ->addColumn('product', 'boolean')
            ->create();

        $this->insertOnCreate($this->addTablePrefix('order_line_item_type'), [
            ['id' => 1, 'name' => 'Product', 'delivery' => 0, 'discount' => 0, 'product' => 1],
            ['id' => 2, 'name' => 'Delivery', 'delivery' => 1, 'discount' => 0, 'product' => 0],
            ['id' => 3, 'name' => 'Discount', 'delivery' => 0, 'discount' => 1, 'product' => 0],
        ]);

        $this->table($this->addTablePrefix('order_line_item'))
            ->addLinkedColumn('type_id', $this->addTablePrefix('order_line_item_type'), 'id')
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id')
            ->addLinkedColumn('product_version_id', $this->addTablePrefix('product_version'), 'id')
            ->addLinkedColumn('status_id', $this->addTablePrefix('order_item_status'), 'id')
            ->addLinkedColumn('vat_rate_id', $this->addTablePrefix('product_vat_rate'), 'id')
            ->addColumn('quantity', 'integer')
            ->addColumn('price', 'float')
            ->addColumn('estimated_delivery_date', 'date', ['null' => true])
            ->addColumn('date_dispatched', 'datetime', ['null' => true])
            ->addColumn('tracking_number', 'string', ['null' => true])
            ->addColumn('tracking_type', 'string', ['null' => true])
            ->create();

        $this->table($this->addTablePrefix('order_payment'))
            ->addLinkedColumn('payment_id', $this->addTablePrefix('payment'), 'id')
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id')
            ->create();

        $this->table($this->addTablePrefix('order_note'))
            ->addColumn('date', 'datetime')
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id')
            ->addLinkedColumn('user_id', $this->addTablePrefix('user'), 'id')
            ->addColumn('note', 'text')
            ->create();

        $this->table($this->addTablePrefix('cart'))
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id', ['null' => true])
            ->update();
    }
}
