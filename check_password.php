<?php
require 'webroot/index.php';

$db = getDbConnection();

$stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute(['greisson']);
$user = $stmt->fetch();

if ($user) {
    echo "Password hash: " . $user['password'] . "\n";
    echo "Verify '123456': " . (password_verify('123456', $user['password']) ? 'YES' : 'NO') . "\n";
    
    // Tentar outras senhas comuns
    $passwords = ['123456', 'password', 'greisson', 'admin', 'admin123'];
    foreach ($passwords as $pass) {
        if (password_verify($pass, $user['password'])) {
            echo "✅ Senha correta encontrada: '$pass'\n";
            break;
        }
    }
    
    // Atualizar senha para 123456
    $newHash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $db->prepare('UPDATE users SET password = ? WHERE username = ?');
    $stmt->execute([$newHash, 'greisson']);
    echo "✅ Senha atualizada para '123456'\n";
} else {
    echo "❌ Usuário não encontrado\n";
}
?>