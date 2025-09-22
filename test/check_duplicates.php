<?php
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICAÇÃO DE DUPLICATAS NO BANCO ===\n\n";
    
    // Verificar se há IDs duplicados na tabela studies
    $stmt = $pdo->query("SELECT id, COUNT(*) as count FROM studies GROUP BY id HAVING COUNT(*) > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "Nenhum ID duplicado encontrado na tabela studies.\n";
    } else {
        echo "IDs duplicados encontrados:\n";
        foreach ($duplicates as $duplicate) {
            echo "ID: {$duplicate['id']} aparece {$duplicate['count']} vezes\n";
        }
    }
    
    echo "\n=== TODOS OS REGISTROS DA TABELA STUDIES ===\n";
    $stmt = $pdo->query("SELECT * FROM studies ORDER BY id");
    $allStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($allStudies as $study) {
        echo "ID: {$study['id']} | Student ID: {$study['student_id']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
    }
    
    echo "\n=== VERIFICAÇÃO ESPECÍFICA DO ID 1 ===\n";
    $stmt = $pdo->prepare("SELECT * FROM studies WHERE id = ?");
    $stmt->execute([1]);
    $study1Records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Registros com ID 1: " . count($study1Records) . "\n";
    foreach ($study1Records as $record) {
        echo "ID: {$record['id']} | Student ID: {$record['student_id']} | Date: {$record['study_date']} | Wins: {$record['wins']} | Losses: {$record['losses']} | P&L: {$record['profit_loss']}\n";
    }
    
    echo "\n=== VERIFICAÇÃO ESPECÍFICA DO ID 3 ===\n";
    $stmt = $pdo->prepare("SELECT * FROM studies WHERE id = ?");
    $stmt->execute([3]);
    $study3Records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Registros com ID 3: " . count($study3Records) . "\n";
    foreach ($study3Records as $record) {
        echo "ID: {$record['id']} | Student ID: {$record['student_id']} | Date: {$record['study_date']} | Wins: {$record['wins']} | Losses: {$record['losses']} | P&L: {$record['profit_loss']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>