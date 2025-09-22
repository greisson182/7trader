<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/backtest.db');

// Criar tabela users
$pdo->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(10) NOT NULL DEFAULT "student" CHECK (role IN ("admin", "student")),
    student_id INTEGER NULL,
    active BOOLEAN NOT NULL DEFAULT 1,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);');

// Criar usuário administrador padrão
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, email, password, role, active, created, modified) 
                      VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
$stmt->execute(['admin', 'admin@backtest.com', $adminPassword, 'admin', 1]);

echo "Tabela users criada com sucesso!\n";
echo "Usuário admin criado:\n";
echo "Username: admin\n";
echo "Password: admin123\n";
echo "Email: admin@backtest.com\n";
?>