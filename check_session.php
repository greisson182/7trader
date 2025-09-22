<?php
session_start();

echo "=== STATUS DA SESSÃO ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";

echo "\n=== DADOS DA SESSÃO ===\n";
if (empty($_SESSION)) {
    echo "Nenhuma sessão ativa\n";
} else {
    foreach ($_SESSION as $key => $value) {
        if (is_array($value)) {
            echo "$key: " . print_r($value, true) . "\n";
        } else {
            echo "$key: $value\n";
        }
    }
}

echo "\n=== COOKIES ===\n";
if (empty($_COOKIE)) {
    echo "Nenhum cookie encontrado\n";
} else {
    foreach ($_COOKIE as $key => $value) {
        echo "$key: $value\n";
    }
}

// Verificar se há usuário logado usando o método da aplicação
require_once 'config/bootstrap.php';

function getDbConnection() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=backtest_db;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

echo "\n=== VERIFICAÇÃO DE LOGIN ===\n";
if (isset($_SESSION['user_id'])) {
    $db = getDbConnection();
    if ($db) {
        $stmt = $db->prepare("SELECT id, username, email, role, active, student_id FROM users WHERE id = ? AND active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "Usuário logado:\n";
            echo "ID: {$user['id']}\n";
            echo "Username: {$user['username']}\n";
            echo "Email: {$user['email']}\n";
            echo "Role: {$user['role']}\n";
            echo "Student ID: " . ($user['student_id'] ?? 'NULL') . "\n";
        } else {
            echo "Usuário não encontrado no banco\n";
        }
    } else {
        echo "Erro ao conectar com o banco\n";
    }
} else {
    echo "Nenhum user_id na sessão\n";
}
?>