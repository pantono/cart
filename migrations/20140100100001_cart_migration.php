<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class CartMigration extends BasePantonoMigration
{
    public function up(): void
    {
        $this->table($this->addTablePrefix('order_status'))
            ->addColumn('name', 'string')
            ->addColumn('completed', 'boolean')
            ->addColumn('cancelled', 'boolean')
            ->addColumn('pending', 'boolean')
            ->create();

        if ($this->isMigratingUp()) {
            $this->insertOnCreate($this->addTablePrefix('order_status'), [
                ['id' => 1, 'name' => 'Pending', 'completed' => 0, 'cancelled' => 0, 'pending' => 1],
                ['id' => 2, 'name' => 'Preparing', 'completed' => 0, 'cancelled' => 0, 'pending' => 1],
                ['id' => 3, 'name' => 'Dispatched', 'completed' => 1, 'cancelled' => 0, 'pending' => 0],
                ['id' => 4, 'name' => 'Cancelled', 'completed' => 0, 'cancelled' => 1, 'pending' => 0],
                ['id' => 5, 'name' => 'Partial Dispatch', 'completed' => 0, 'cancelled' => 0, 'pending' => 0],
            ]);

        }
        $this->table($this->addTablePrefix('order'))
            ->addColumn('date_created', 'datetime')
            ->addColumn('reference', 'string')
            ->addColumn('date_updated', 'datetime')
            ->addLinkedColumn('status_id', $this->addTablePrefix('order_status'), 'id')
            ->addLinkedColumn('billing_location', $this->addTablePrefix('location'), 'id')
            ->addLinkedColumn('delivery_location', $this->addTablePrefix('location'), 'id')
            ->addLinkedColumn('customer_id', $this->addTablePrefix('customer'), 'id')
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

        $this->table($this->addTablePrefix('order_item'))
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id')
            ->addLinkedColumn('product_version_id', $this->addTablePrefix('product_version'), 'id')
            ->addLinkedColumn('status_id', $this->addTablePrefix('order_item_status'), 'id')
            ->addColumn('quantity', 'integer')
            ->addColumn('price', 'float')
            ->addColumn('vat', 'float')
            ->addColumn('date_dispatched', 'datetime', ['null' => true])
            ->addColumn('tracking_number', 'string', ['null' => true])
            ->addColumn('tracking_type', 'string', ['null' => true])
            ->create();

        $this->table($this->addTablePrefix('order_payment'))
            ->addLinkedColumn('payment_id', $this->addTablePrefix('payment'), 'id')
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id')
            ->create();

        $this->table($this->addTablePrefix('delivery_speed'))
            ->addColumn('name', 'string')
            ->addColumn('live', 'boolean')
            ->create();

        $this->table($this->addTablePrefix('delivery_type'))
            ->addColumn('name', 'string')
            ->addColumn('live', 'boolean')
            ->create();

        $this->table($this->addTablePrefix('delivery_estimate'))
            ->addLinkedColumn('type_id', $this->addTablePrefix('delivery_type'), 'id')
            ->addLinkedColumn('speed_id', $this->addTablePrefix('delivery_speed'), 'id')
            ->addColumn('order_day', 'integer')
            ->addColumn('delivery_day', 'integer')
            ->addColumn('cutoff', 'time')
            ->create();

        $this->table($this->addTablePrefix('delivery_cost'))
            ->addLinkedColumn('type_id', $this->addTablePrefix('delivery_type'), 'id')
            ->addLinkedColumn('speed_id', $this->addTablePrefix('delivery_speed'), 'id')
            ->addLinkedColumn('country_id', $this->addTablePrefix('country'), 'id')
            ->addColumn('cost', 'decimal')
            ->addColumn('min_weight', 'float')
            ->addColumn('max_weight', 'float')
            ->addColumn('priority', 'integer')
            ->addLinkedColumn('vat_rate_id', 'vat_rate', 'id')
            ->create();

        $this->table($this->addTablePrefix('cart'))
            ->addColumn('session_id', 'string')
            ->addColumn('date_updated', 'datetime')
            ->addColumn('date_created', 'datetime')
            ->addLinkedColumn('delivery_type_id', $this->addTablePrefix('delivery_type'), 'id', ['null' => true])
            ->addLinkedColumn('delivery_speed_id', $this->addTablePrefix('delivery_speed'), 'id', ['null' => true])
            ->addLinkedColumn('user_id', $this->addTablePrefix('users'), 'id', ['null' => true])
            ->addLinkedColumn('country_id', 'country', 'id', ['null' => true])
            ->addForeignKey('session_id', $this->addTablePrefix('sessions'), 'sess_id')
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id', ['null' => true])
            ->create();

        $this->table($this->addTablePrefix('cart_code'), ['id' => false])
            ->addLinkedColumn('cart_id', $this->addTablePrefix('cart'), 'id')
            ->addLinkedColumn('code_id', $this->addTablePrefix('codes'), 'id')
            ->addColumn('date_added', 'datetime')
            ->create();

        $this->table($this->addTablePrefix('cart_item'))
            ->addLinkedColumn('cart_id', $this->addTablePrefix('cart'), 'id')
            ->addLinkedColumn('product_id', $this->addTablePrefix('product'), 'id')
            ->addColumn('quantity', 'integer')
            ->addColumn('date_added', 'datetime')
            ->create();

        $this->table($this->addTablePrefix('cart_payment'), ['id' => false])
            ->addLinkedColumn('cart_id', $this->addTablePrefix('cart'), 'id')
            ->addLinkedColumn('payment_id', $this->addTablePrefix('payment'), 'id')
            ->create();

    }
}
