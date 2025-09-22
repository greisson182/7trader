<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCurrencyToMarkets extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('markets');
        $table->addColumn('currency', 'string', [
            'limit' => 3,
            'null' => false,
            'default' => 'BRL',
            'comment' => 'Moeda do mercado (BRL, USD, EUR, etc.)',
            'after' => 'description'
        ])
        ->update();
    }
}