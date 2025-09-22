<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMarketToStudies extends AbstractMigration
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
        $table = $this->table('studies');
        $table->addColumn('market_id', 'integer', [
            'null' => true,
            'comment' => 'ID do mercado associado ao estudo',
            'after' => 'student_id'
        ])
        ->addForeignKey('market_id', 'markets', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE'
        ])
        ->update();

        // Definir WINFUT como padrão para estudos existentes
        $this->execute("
            UPDATE studies 
            SET market_id = (SELECT id FROM markets WHERE code = 'WINFUT' LIMIT 1) 
            WHERE market_id IS NULL
        ");
    }
}