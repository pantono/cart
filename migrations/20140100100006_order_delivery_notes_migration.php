<?php
declare(strict_types=1);

use Pantono\Database\Migration\Base\BasePantonoMigration;

final class OrderDeliveryNotesMigration extends BasePantonoMigration
{
    public function change(): void
    {
        $this->table('order')
            ->addColumn('delivery_notes', 'text', ['null' => true])
            ->update();
    }
}
