<?php
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ESTRUTURA DA TABELA STUDIES ===\n";
    $result = $pdo->query('PRAGMA table_info(studies)');
    while($row = $result->fetch()) {
        echo $row['name'] . ' (' . $row['type'] . ')' . "\n";
    }
    
    echo "\n=== DADOS REAIS DA TABELA STUDIES ===\n";
    $stmt = $pdo->query('SELECT * FROM studies ORDER BY id');
    $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($studies as $study) {
        echo "ID: {$study['id']} | Student ID: {$study['student_id']} | Date: {$study['study_date']}";
        if (isset($study['gain'])) echo " | Gain: {$study['gain']}";
        if (isset($study['loss'])) echo " | Loss: {$study['loss']}";
        if (isset($study['pl'])) echo " | P&L: {$study['pl']}";
        if (isset($study['market_id'])) echo " | Market ID: {$study['market_id']}";
        echo "\n";
    }
    
    echo "\n=== ESTRUTURA DA TABELA STUDENTS ===\n";
    $result = $pdo->query('PRAGMA table_info(students)');
    while($row = $result->fetch()) {
        echo $row['name'] . ' (' . $row['type'] . ')' . "\n";
    }
    
    echo "\n=== DADOS DA TABELA STUDENTS ===\n";
    $stmt = $pdo->query('SELECT * FROM students ORDER BY id');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($students as $student) {
        echo "ID: {$student['id']} | Name: {$student['name']}\n";
    }
    
    echo "\n=== ESTRUTURA DA TABELA MARKETS ===\n";
    $result = $pdo->query('PRAGMA table_info(markets)');
    while($row = $result->fetch()) {
        echo $row['name'] . ' (' . $row['type'] . ')' . "\n";
    }
    
    echo "\n=== DADOS DA TABELA MARKETS ===\n";
    $stmt = $pdo->query('SELECT * FROM markets ORDER BY id');
    $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($markets as $market) {
        echo "ID: {$market['id']} | Name: {$market['name']} | Currency: {$market['currency']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>