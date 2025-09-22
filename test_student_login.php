<?php
session_start();

// Simular dados de login do estudante
$loginData = [
    'username' => 'greisson',
    'password' => 'admin123'  // Assumindo que a senha é a mesma
];

echo "=== TESTE DE LOGIN DO ESTUDANTE ===\n";
echo "Tentando fazer login com: {$loginData['username']}\n\n";

// Conectar ao banco
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Buscar usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1");
$stmt->execute([$loginData['username'], $loginData['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "❌ Usuário não encontrado\n";
    exit;
}

echo "✅ Usuário encontrado:\n";
echo "ID: {$user['id']}\n";
echo "Username: {$user['username']}\n";
echo "Role: {$user['role']}\n";
echo "Student ID: " . ($user['student_id'] ?? 'NULL') . "\n\n";

// Verificar senha
if (!password_verify($loginData['password'], $user['password'])) {
    echo "❌ Senha incorreta\n";
    
    // Testar com hash direto para debug
    echo "Hash no banco: {$user['password']}\n";
    echo "Testando senha: {$loginData['password']}\n";
    
    // Criar novo hash para comparação
    $newHash = password_hash($loginData['password'], PASSWORD_DEFAULT);
    echo "Novo hash: $newHash\n";
    echo "Verificação com novo hash: " . (password_verify($loginData['password'], $newHash) ? 'OK' : 'FALHA') . "\n";
    exit;
}

echo "✅ Senha correta\n\n";

// Simular login (criar sessão)
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

echo "=== SESSÃO CRIADA ===\n";
echo "Session ID: " . session_id() . "\n";
foreach ($_SESSION as $key => $value) {
    echo "$key: $value\n";
}

echo "\n=== TESTANDO ACESSO AO ADMIN ===\n";

// Simular verificação de acesso
if (isset($_SESSION['user_id'])) {
    echo "✅ Usuário está logado\n";
    
    if ($_SESSION['role'] === 'student') {
        echo "✅ Usuário é estudante\n";
        echo "🎯 Deveria ter acesso ao /admin\n";
    } else {
        echo "ℹ️ Usuário é {$_SESSION['role']}\n";
    }
} else {
    echo "❌ Usuário não está logado\n";
}

echo "\n=== RESULTADO ===\n";
echo "Login realizado com sucesso para o estudante {$user['username']}\n";
echo "Sessão ativa com ID: " . session_id() . "\n";
?>