<?php
echo "=== DEBUG DA SESSÃO ===\n";

// Iniciar sessão
session_start();
echo "Session ID inicial: " . session_id() . "\n";
echo "Dados da sessão inicial:\n";
var_dump($_SESSION);

// Conectar ao banco
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Fazer login manual
$username = 'greisson';
$password = 'admin123';

echo "\n=== FAZENDO LOGIN MANUAL ===\n";
$stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1");
$stmt->execute([$username, $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    
    echo "✅ Login realizado com sucesso!\n";
    echo "Session ID após login: " . session_id() . "\n";
    echo "Dados da sessão após login:\n";
    var_dump($_SESSION);
    
    // Verificar se está logado
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    echo "\nVerificação isLoggedIn(): " . ($isLoggedIn ? 'true' : 'false') . "\n";
    
    // Salvar session ID para teste
    file_put_contents('debug_session_id.txt', session_id());
    echo "Session ID salvo em debug_session_id.txt\n";
    
} else {
    echo "❌ Falha no login\n";
}
?>