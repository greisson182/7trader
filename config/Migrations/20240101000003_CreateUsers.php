<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('username', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
            'comment' => 'Username for login'
        ]);
        $table->addColumn('email', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
            'comment' => 'User email address'
        ]);
        $table->addColumn('password', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'comment' => 'Hashed password'
        ]);
        $table->addColumn('role', 'enum', [
            'values' => ['admin', 'student'],
            'default' => 'student',
            'null' => false,
            'comment' => 'User role: admin or student'
        ]);
        $table->addColumn('student_id', 'integer', [
            'default' => null,
            'null' => true,
            'comment' => 'Reference to students table if role is student'
        ]);
        $table->addColumn('active', 'boolean', [
            'default' => true,
            'null' => false,
            'comment' => 'Whether the user account is active'
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        
        $table->addIndex(['username'], ['unique' => true]);
        $table->addIndex(['email'], ['unique' => true]);
        $table->addIndex(['student_id']);
        $table->create();
    }
}