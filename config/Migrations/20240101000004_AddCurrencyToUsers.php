<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCurrencyToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('currency', 'enum', [
            'values' => ['BRL', 'USD'],
            'default' => 'BRL',
            'null' => false,
            'comment' => 'User preferred currency: BRL or USD',
            'after' => 'active'
        ]);
        $table->update();
    }
}