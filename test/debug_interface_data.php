<?php
try {
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SIMULANDO EXATAMENTE O QUE O CONTROLLER FAZ ===\n\n";
    
    // Consulta exata do controller
    $stmt = $pdo->query("
        SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
        FROM studies s 
        LEFT JOIN students st ON s.student_id = st.id 
        LEFT JOIN markets m ON s.market_id = m.id
        ORDER BY s.study_date DESC
    ");
    
    $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Adicionar dados do usuário a cada estudo (como faz o controller)
    foreach ($studies as &$study) {
        $study['user'] = [
            'currency' => $study['currency'] ?? 'BRL'
        ];
    }
    
    echo "Total de estudos após consulta: " . count($studies) . "\n\n";
    
    // Agrupar estudos por mês/ano (como faz o controller)
    $studiesByMonth = [];
    foreach ($studies as $study) {
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
    
    // Ordenar por mês/ano (mais recente primeiro)
    krsort($studiesByMonth);
    
    echo "=== DADOS AGRUPADOS POR MÊS (COMO ENVIADO PARA O TEMPLATE) ===\n";
    foreach ($studiesByMonth as $monthKey => $monthData) {
        echo "\n--- {$monthData['display']} ---\n";
        echo "Total de estudos: {$monthData['total_studies']}\n";
        echo "Total Wins: {$monthData['total_wins']}\n";
        echo "Total Losses: {$monthData['total_losses']}\n";
        echo "Total P&L: {$monthData['total_profit_loss']}\n";
        
        echo "Estudos individuais:\n";
        foreach ($monthData['studies'] as $study) {
            echo "  ID: {$study['id']} | Student: {$study['student_name']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
        }
    }
    
    // Verificar especificamente Janeiro 2025
    echo "\n=== FOCO EM JANEIRO 2025 ===\n";
    if (isset($studiesByMonth['2025-01'])) {
        $jan2025 = $studiesByMonth['2025-01'];
        echo "Estudos em Janeiro 2025: {$jan2025['total_studies']}\n";
        echo "Total Wins: {$jan2025['total_wins']}\n";
        echo "Total Losses: {$jan2025['total_losses']}\n";
        echo "Total P&L: {$jan2025['total_profit_loss']}\n";
        
        echo "\nDetalhes de cada estudo:\n";
        foreach ($jan2025['studies'] as $study) {
            echo "ID: {$study['id']} | Student: {$study['student_name']} | Market: {$study['market_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']} | P&L: {$study['profit_loss']}\n";
        }
    } else {
        echo "Nenhum estudo encontrado em Janeiro 2025.\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>