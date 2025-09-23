<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMarkets extends AbstractMigration
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
        $table->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false,
            'comment' => 'Nome do mercado'
        ])
        ->addColumn('code', 'string', [
            'limit' => 20,
            'null' => false,
            'comment' => 'Código do mercado'
        ])
        ->addColumn('description', 'text', [
            'null' => true,
            'comment' => 'Descrição do mercado'
        ])
        ->addColumn('active', 'boolean', [
            'default' => true,
            'null' => false,
            'comment' => 'Se o mercado está ativo'
        ])
        ->addColumn('created_at', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false
        ])
        ->addColumn('updated_at', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'update' => 'CURRENT_TIMESTAMP',
            'null' => false
        ])
        ->addColumn('currency', 'string', [
            'limit' => 3,
            'null' => false,
            'default' => 'BRL',
            'comment' => 'Moeda do mercado (BRL, USD, EUR, etc.)',
            'after' => 'description'
        ])
        ->addIndex(['code'], ['unique' => true])
        ->create();

        // Inserir os mercados padrão
        $this->execute("
            INSERT INTO markets (name, code, description, active) VALUES 
            ('WIN Futuro', 'WINFUT', 'Contrato futuro do índice Bovespa', 1),
            ('WDO Futuro', 'WDOFUT', 'Contrato futuro de dólar comercial', 1)
        ");
    }
}