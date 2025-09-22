<?php
echo "=== TESTE SIMPLES ===\n";

try {
    $dsn = "mysql:host=localhost;dbname=backtest;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão OK\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM studies");
    $result = $stmt->fetch();
    echo "Total de estudos: " . $result['total'] . "\n";
    
    $stmt = $pdo->query("SELECT id, student_id, study_date FROM studies ORDER BY id");
    $studies = $stmt->fetchAll();
    
    echo "Estudos encontrados:\n";
    foreach ($studies as $study) {
        echo "ID: {$study['id']}, Student: {$study['student_id']}, Date: {$study['study_date']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>