<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Atualizar senha do estudante greisson para admin123
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'greisson'");
    $stmt->execute([$newPassword]);
    
    echo "✅ Senha do usuário 'greisson' atualizada para 'admin123'\n";
    
    // Verificar se funcionou
    $stmt = $pdo->prepare("SELECT username, password FROM users WHERE username = 'greisson'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "✅ Verificação: Senha 'admin123' funciona para o usuário 'greisson'\n";
    } else {
        echo "❌ Erro: Senha não foi atualizada corretamente\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>