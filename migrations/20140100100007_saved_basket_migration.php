<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class SavedBasketMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->tablePrefix('cart_saved')
            ->addLinkedColumn('user_id', $this->addTablePrefix('user'), 'id', ['null' => false])
            ->addColumn('date_created', 'datetime')
            ->addColumn('name', 'string')
            ->addColumn('deleted', 'boolean')
            ->create();

        $this->tablePrefix('cart_saved_item')
            ->addLinkedColumn('saved_cart_id', $this->addTablePrefix('cart_saved'), 'id', ['null' => false])
            ->addLinkedColumn('product_id', $this->addTablePrefix('product'), 'id', ['null' => false])
            ->addColumn('quantity', 'integer')
            ->create();
    }
}
