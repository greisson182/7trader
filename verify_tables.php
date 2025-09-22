<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/backtest.db');

// Verificar tabelas existentes
$stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Tabelas existentes no banco:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}

// Verificar estrutura da tabela users se existir
if (in_array('users', $tables)) {
    echo "\nEstrutura da tabela users:\n";
    $stmt = $pdo->query("PRAGMA table_info(users)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['name']} ({$column['type']})\n";
    }
    
    // Verificar usuários existentes
    echo "\nUsuários cadastrados:\n";
    $stmt = $pdo->query("SELECT id, username, email, role, active FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}, Active: {$user['active']}\n";
    }
} else {
    echo "\nTabela users NÃO encontrada!\n";
}
?>