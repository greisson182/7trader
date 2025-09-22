<?php
// Debug da consulta atual do controller
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING CURRENT CONTROLLER QUERY ===\n\n";
    
    // Simulando a consulta exata do controller
    $sql = "SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
            FROM studies s 
            LEFT JOIN students st ON s.student_id = st.id 
            LEFT JOIN markets m ON s.market_id = m.id
            ORDER BY s.study_date DESC";
    
    echo "Query SQL:\n$sql\n\n";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de registros retornados: " . count($studies) . "\n\n";
    
    echo "=== TODOS OS ESTUDOS ===\n";
    foreach ($studies as $study) {
        echo "ID: {$study['id']} | Student: {$study['student_name']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Gain: {$study['gain']} | Loss: {$study['loss']}\n";
    }
    
    echo "\n=== ESTUDOS DO LUAN SILVA ===\n";
    $luanStudies = array_filter($studies, function($study) {
        return $study['student_name'] === 'Luan Silva';
    });
    
    echo "Total de estudos do Luan: " . count($luanStudies) . "\n";
    foreach ($luanStudies as $study) {
        echo "ID: {$study['id']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Gain: {$study['gain']} | Loss: {$study['loss']} | P&L: R$ {$study['pl']}\n";
    }
    
    echo "\n=== VERIFICANDO DUPLICATAS POR ID ===\n";
    $ids = array_column($studies, 'id');
    $duplicateIds = array_diff_assoc($ids, array_unique($ids));
    
    if (empty($duplicateIds)) {
        echo "Nenhuma duplicata encontrada na consulta SQL.\n";
    } else {
        echo "IDs duplicados encontrados: " . implode(', ', array_unique($duplicateIds)) . "\n";
    }
    
    echo "\n=== VERIFICANDO ESTUDO ID 3 ESPECIFICAMENTE ===\n";
    $study3Records = array_filter($studies, function($study) {
        return $study['id'] == 3;
    });
    
    echo "Registros com ID 3: " . count($study3Records) . "\n";
    foreach ($study3Records as $record) {
        echo "ID: {$record['id']} | Student ID: {$record['student_id']} | Market ID: {$record['market_id']} | Student: {$record['student_name']} | Market: {$record['market_name']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>