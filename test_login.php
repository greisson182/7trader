<?php
// Conectar diretamente ao SQLite
try {
    $sqliteDb = dirname(__FILE__) . '/backtest.db';
    $db = new PDO("sqlite:$sqliteDb");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Testar busca do usuário
$stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1");
$stmt->execute(['admin', 'admin']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "=== TESTE DE LOGIN ===\n";
echo "Usuário encontrado: " . ($user ? 'SIM' : 'NÃO') . "\n";

if ($user) {
    echo "ID: " . $user['id'] . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Active: " . $user['active'] . "\n";
    echo "Password hash: " . $user['password'] . "\n";
    
    // Testar verificação da senha
    $password = 'admin123';
    $isValid = password_verify($password, $user['password']);
    echo "Senha '$password' válida: " . ($isValid ? 'SIM' : 'NÃO') . "\n";
    
    if (!$isValid) {
        // Testar com hash direto
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        echo "Novo hash gerado: $newHash\n";
        echo "Verificação com novo hash: " . (password_verify($password, $newHash) ? 'SIM' : 'NÃO') . "\n";
    }
} else {
    echo "Usuário não encontrado!\n";
}
?>