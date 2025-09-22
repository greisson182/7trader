<?php
session_start();

// Simular login do estudante
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'greisson';
$_SESSION['user_role'] = 'student';

echo "=== DEBUG DO DASHBOARD ===\n";
echo "Sessão criada:\n";
foreach ($_SESSION as $key => $value) {
    echo "  $key: $value\n";
}

// Conectar ao banco
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Verificar dados do usuário
echo "\n=== VERIFICANDO USUÁRIO ===\n";
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND active = 1");
$stmt->execute([2]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "✅ Usuário encontrado:\n";
    echo "  ID: {$user['id']}\n";
    echo "  Username: {$user['username']}\n";
    echo "  Role: {$user['role']}\n";
    echo "  Student ID: " . ($user['student_id'] ?? 'NULL') . "\n";
    echo "  Active: {$user['active']}\n";
    
    if ($user['student_id']) {
        echo "\n=== VERIFICANDO ESTUDANTE ===\n";
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$user['student_id']]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            echo "✅ Estudante encontrado:\n";
            echo "  ID: {$student['id']}\n";
            echo "  Name: {$student['name']}\n";
            echo "  Email: {$student['email']}\n";
        } else {
            echo "❌ Estudante não encontrado com ID: {$user['student_id']}\n";
        }
    } else {
        echo "❌ Usuário não tem student_id associado\n";
    }
} else {
    echo "❌ Usuário não encontrado\n";
}

// Testar acesso ao dashboard
echo "\n=== TESTANDO DASHBOARD ===\n";
$sessionId = session_id();
file_put_contents('dashboard_session_id.txt', $sessionId);
echo "Session ID salvo: $sessionId\n";
echo "Para testar: Invoke-WebRequest -Uri 'http://localhost:8000/admin/students/dashboard/1' -Headers @{'Cookie'='PHPSESSID=$sessionId'}\n";
?>