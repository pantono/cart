<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class StockReservationMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->tablePrefix('cart_stock_reservation')
            ->addLinkedColumn('cart_id', $this->addTablePrefix('cart'), 'id')
            ->addColumn('date_reserved', 'datetime')
            ->addColumn('date_expires', 'datetime')
            ->addColumn('quantity', 'integer')
            ->addLinkedColumn('product_id', $this->addTablePrefix('product'), 'id')
            ->addIndex('date_expires')
            ->create();
    }
}
