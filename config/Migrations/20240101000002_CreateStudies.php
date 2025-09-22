<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateStudies extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('studies');
        $table->addColumn('student_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('market_date', 'date', [
            'default' => null,
            'null' => false,
            'comment' => 'The date of the market being studied'
        ]);
        $table->addColumn('study_date', 'date', [
            'default' => null,
            'null' => false,
            'comment' => 'The date when the study was conducted'
        ]);
        $table->addColumn('wins', 'integer', [
            'default' => 0,
            'null' => false,
            'comment' => 'Number of winning trades'
        ]);
        $table->addColumn('losses', 'integer', [
            'default' => 0,
            'null' => false,
            'comment' => 'Number of losing trades'
        ]);
        $table->addColumn('profit_loss', 'decimal', [
            'default' => 0.00,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Total profit/loss amount'
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex(['student_id']);
        $table->addIndex(['market_date']);
        $table->addIndex(['study_date']);
        $table->addForeignKey('student_id', 'students', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION'
        ]);
        $table->create();
    }
}