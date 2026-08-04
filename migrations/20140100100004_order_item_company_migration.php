<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class OrderItemCompanyMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->tablePrefix('order_line_item')
            ->addLinkedColumn('company_id', $this->addTablePrefix('company'), 'id', ['null' => true])
            ->addIndex('company_id')
            ->update();
    }
}
