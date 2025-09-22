<?php
require 'webroot/index.php';

$db = getDbConnection();

echo "=== ESTRUTURA DA TABELA USERS ===\n";
$stmt = $db->query('DESCRIBE users');
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== DADOS DOS USUÁRIOS ===\n";
$stmt = $db->query('SELECT id, username, email, role, active FROM users');
while($row = $stmt->fetch()) {
    echo "ID: {$row['id']}, Username: {$row['username']}, Email: {$row['email']}, Role: {$row['role']}, Active: {$row['active']}\n";
}

echo "\n=== TESTANDO LOGIN COM EMAIL ===\n";
$email = 'aluno1@teste.com';
$password = '123456';

$stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1");
$stmt->execute([$email, $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "Usuário encontrado: " . print_r($user, true) . "\n";
    
    if (password_verify($password, $user['password'])) {
        echo "✅ Senha correta!\n";
    } else {
        echo "❌ Senha incorreta!\n";
        echo "Hash da senha no banco: " . $user['password'] . "\n";
        echo "Hash da senha '123456': " . password_hash('123456', PASSWORD_DEFAULT) . "\n";
    }
} else {
    echo "❌ Usuário não encontrado!\n";
}
?>