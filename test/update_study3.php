<?php
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Atualizar estudo ID 3 com dados diferentes
    $stmt = $pdo->prepare("UPDATE studies SET wins = ?, losses = ?, profit_loss = ? WHERE id = ?");
    $stmt->execute([3, 2, -150.50, 3]);
    
    echo "Estudo ID 3 atualizado:\n";
    echo "- Wins: 3\n";
    echo "- Losses: 2\n";
    echo "- P&L: -150.50\n";
    
    // Verificar a atualização
    $stmt = $pdo->prepare("SELECT * FROM studies WHERE id = ?");
    $stmt->execute([3]);
    $study = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($study) {
        echo "\nVerificação:\n";
        echo "ID: {$study['id']} | Student ID: {$study['student_id']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>