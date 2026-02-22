<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class CartMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->table($this->addTablePrefix('delivery_speed'))
            ->addColumn('name', 'string')
            ->addColumn('live', 'boolean')
            ->create();

        $this->table($this->addTablePrefix('delivery_estimate'))
            ->addLinkedColumn('speed_id', $this->addTablePrefix('delivery_speed'), 'id')
            ->addLinkedColumn('country_id', $this->addTablePrefix('country'), 'id')
            ->addColumn('order_day', 'integer')
            ->addColumn('delivery_day', 'integer')
            ->addColumn('cutoff', 'time')
            ->create();

        $this->table($this->addTablePrefix('delivery_cost'))
            ->addLinkedColumn('speed_id', $this->addTablePrefix('delivery_speed'), 'id')
            ->addLinkedColumn('country_id', $this->addTablePrefix('country'), 'id')
            ->addColumn('cost', 'decimal')
            ->addColumn('min_weight', 'float')
            ->addColumn('max_weight', 'float')
            ->addColumn('priority', 'integer')
            ->addLinkedColumn('vat_rate_id', $this->addTablePrefix('product_vat_rate'), 'id')
            ->create();

        $this->table($this->addTablePrefix('cart'))
            ->addColumn('session_id', 'string')
            ->addColumn('date_updated', 'datetime')
            ->addColumn('date_created', 'datetime')
            ->addLinkedColumn('delivery_speed_id', $this->addTablePrefix('delivery_speed'), 'id', ['null' => true])
            ->addLinkedColumn('user_id', $this->addTablePrefix('user'), 'id', ['null' => true])
            ->addLinkedColumn('shipping_location_id', 'location', 'id', ['null' => true])
            ->addLinkedColumn('billing_location_id', 'location', 'id', ['null' => true])
            ->addColumn('forename', 'string', ['null' => true])
            ->addColumn('surname', 'string', ['null' => true])
            ->addColumn('email', 'string', ['null' => true])
            ->addColumn('telephone', 'string', ['null' => true])
            ->addIndex('session_id')
            ->create();

        $this->table($this->addTablePrefix('cart_code'), ['id' => false])
            ->addLinkedColumn('cart_id', $this->addTablePrefix('cart'), 'id')
            ->addLinkedColumn('code_id', $this->addTablePrefix('discount_code'), 'id')
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
