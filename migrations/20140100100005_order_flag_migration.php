<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class OrderFlagMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->table('order_flag_type')
            ->addColumn('name', 'string')
            ->create();

        $this->tablePrefix('order_flag', ['id' => false])
            ->addLinkedColumn('order_id', $this->addTablePrefix('order'), 'id', ['null' => false])
            ->addLinkedColumn('flag_type_id', $this->addTablePrefix('order_flag_type'), 'id', ['null' => false])
            ->create();
    }
}
