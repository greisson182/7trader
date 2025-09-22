<?php
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING LUAN SILVA DATA ===\n\n";
    
    // 1. Verificar dados brutos do Luan Silva
    echo "1. DADOS BRUTOS DO LUAN SILVA:\n";
    $stmt = $pdo->prepare("SELECT * FROM studies WHERE student_id = 4 ORDER BY id");
    $stmt->execute();
    $luanStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de estudos do Luan no banco: " . count($luanStudies) . "\n";
    foreach ($luanStudies as $study) {
        echo "ID: {$study['id']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']} | Market ID: {$study['market_id']}\n";
    }
    
    // 2. Simular a consulta exata do controller
    echo "\n2. CONSULTA EXATA DO CONTROLLER:\n";
    $stmt = $pdo->query("
        SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
        FROM studies s 
        LEFT JOIN students st ON s.student_id = st.id 
        LEFT JOIN markets m ON s.market_id = m.id
        ORDER BY s.study_date DESC
    ");
    $allStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de estudos retornados pela consulta: " . count($allStudies) . "\n";
    
    // Filtrar apenas Luan Silva
    $luanFromQuery = array_filter($allStudies, function($study) {
        return $study['student_name'] === 'Luan Silva';
    });
    
    echo "Estudos do Luan Silva na consulta: " . count($luanFromQuery) . "\n";
    foreach ($luanFromQuery as $study) {
        echo "ID: {$study['id']} | Student: {$study['student_name']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
    }
    
    // 3. Simular o agrupamento por mês
    echo "\n3. SIMULANDO AGRUPAMENTO POR MÊS:\n";
    $studiesByMonth = [];
    foreach ($luanFromQuery as $study) {
        if (!empty($study['study_date'])) {
            $date = new DateTime($study['study_date']);
            $monthYear = $date->format('Y-m');
            $monthYearDisplay = $date->format('F Y');
            
            if (!isset($studiesByMonth[$monthYear])) {
                $studiesByMonth[$monthYear] = [
                    'display' => $monthYearDisplay,
                    'studies' => [],
                    'total_studies' => 0,
                    'total_wins' => 0,
                    'total_losses' => 0,
                    'total_profit_loss' => 0
                ];
            }
            
            $studiesByMonth[$monthYear]['studies'][] = $study;
            $studiesByMonth[$monthYear]['total_studies']++;
            $studiesByMonth[$monthYear]['total_wins'] += $study['wins'] ?? 0;
            $studiesByMonth[$monthYear]['total_losses'] += $study['losses'] ?? 0;
            $studiesByMonth[$monthYear]['total_profit_loss'] += $study['profit_loss'] ?? 0;
        }
    }
    
    foreach ($studiesByMonth as $monthKey => $monthData) {
        echo "Mês: {$monthData['display']}\n";
        echo "  Total de estudos: {$monthData['total_studies']}\n";
        echo "  Total Wins: {$monthData['total_wins']}\n";
        echo "  Total Losses: {$monthData['total_losses']}\n";
        echo "  Total P&L: {$monthData['total_profit_loss']}\n";
        echo "  Estudos individuais:\n";
        foreach ($monthData['studies'] as $study) {
            echo "    ID: {$study['id']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
        }
        echo "\n";
    }
    
    // 4. Verificar se há algum problema com IDs duplicados
    echo "4. VERIFICAÇÃO DE IDs DUPLICADOS:\n";
    $ids = array_column($luanFromQuery, 'id');
    $duplicateIds = array_diff_assoc($ids, array_unique($ids));
    
    if (empty($duplicateIds)) {
        echo "Nenhum ID duplicado encontrado na consulta.\n";
    } else {
        echo "IDs duplicados encontrados: " . implode(', ', array_unique($duplicateIds)) . "\n";
    }
    
    // 5. Verificar dados específicos que aparecem na interface
    echo "\n5. VERIFICAÇÃO DOS DADOS ESPECÍFICOS DA INTERFACE:\n";
    echo "Procurando por estudo com ID 3 (que aparece duplicado na interface):\n";
    
    $study3 = array_filter($allStudies, function($study) {
        return $study['id'] == 3;
    });
    
    foreach ($study3 as $study) {
        echo "ID: {$study['id']} | Student: {$study['student_name']} | Student ID: {$study['student_id']} | Market: {$study['market_name']} | Date: {$study['study_date']}\n";
    }
    
    if (empty($study3)) {
        echo "Estudo ID 3 não encontrado na consulta.\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>