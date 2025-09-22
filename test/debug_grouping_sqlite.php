<?php
echo "=== DEBUG: FUNÇÃO DE AGRUPAMENTO POR MÊS (SQLite) ===\n\n";

try {
    // Conectar ao SQLite
    $pdo = new PDO('sqlite:backtest.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão SQLite OK\n\n";
    
    // 1. Buscar dados originais (mesma query do controller)
    $query = "SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
              FROM studies s 
              LEFT JOIN students st ON s.student_id = st.id 
              LEFT JOIN markets m ON s.market_id = m.id
              ORDER BY s.study_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "1. DADOS ORIGINAIS DA QUERY (" . count($studies) . " estudos):\n";
    foreach ($studies as $study) {
        echo "   ID: {$study['id']} | Student: {$study['student_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']}\n";
    }
    echo "\n";
    
    // 2. Simular a função problemática EXATAMENTE como no controller
    echo "2. PROCESSAMENTO DA FUNÇÃO DE AGRUPAMENTO:\n";
    $studiesByMonth = [];
    
    foreach ($studies as $study) {
        echo "   Processando estudo ID: {$study['id']} - {$study['student_name']} - {$study['study_date']}\n";
        
        if (!empty($study['study_date'])) {
            $date = new \DateTime($study['study_date']);
            $monthYear = $date->format('Y-m');
            $monthYearDisplay = $date->format('F Y');
            
            echo "     -> Mês/Ano: $monthYear ($monthYearDisplay)\n";
            
            if (!isset($studiesByMonth[$monthYear])) {
                $studiesByMonth[$monthYear] = [
                    'display' => $monthYearDisplay,
                    'studies' => [],
                    'total_studies' => 0,
                    'total_wins' => 0,
                    'total_losses' => 0,
                    'total_profit_loss' => 0
                ];
                echo "     -> Criando novo grupo para $monthYear\n";
            } else {
                echo "     -> Grupo $monthYear já existe\n";
            }
            
            // Esta é a linha que pode estar causando duplicação
            $studiesByMonth[$monthYear]['studies'][] = $study;
            $studiesByMonth[$monthYear]['total_studies']++;
            $studiesByMonth[$monthYear]['total_wins'] += $study['wins'] ?? 0;
            $studiesByMonth[$monthYear]['total_losses'] += $study['losses'] ?? 0;
            $studiesByMonth[$monthYear]['total_profit_loss'] += $study['profit_loss'] ?? 0;
            
            echo "     -> Adicionado ao grupo. Total de estudos no grupo: {$studiesByMonth[$monthYear]['total_studies']}\n";
            echo "     -> IDs no grupo agora: ";
            foreach ($studiesByMonth[$monthYear]['studies'] as $s) {
                echo $s['id'] . " ";
            }
            echo "\n";
        }
        echo "\n";
    }
    
    // 3. Mostrar resultado final
    echo "3. RESULTADO FINAL DO AGRUPAMENTO:\n";
    foreach ($studiesByMonth as $monthYear => $monthData) {
        echo "   $monthYear ({$monthData['display']}):\n";
        echo "     Total de estudos: {$monthData['total_studies']}\n";
        echo "     Estudos no array:\n";
        
        $studyIds = [];
        foreach ($monthData['studies'] as $index => $study) {
            echo "       [$index] ID: {$study['id']} - {$study['student_name']} - {$study['study_date']}\n";
            $studyIds[] = $study['id'];
        }
        
        // Verificar duplicações neste mês
        $uniqueIds = array_unique($studyIds);
        if (count($studyIds) !== count($uniqueIds)) {
            echo "     *** DUPLICAÇÃO DETECTADA! ***\n";
            $duplicates = array_diff_assoc($studyIds, $uniqueIds);
            echo "     IDs duplicados: " . implode(', ', $duplicates) . "\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>