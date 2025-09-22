<?php
require_once 'vendor/autoload.php';

// Configuração do banco
$config = [
    'host' => 'localhost',
    'database' => 'backtest',
    'username' => 'root',
    'password' => '',
    'driver' => 'mysql'
];

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUG: FUNÇÃO DE AGRUPAMENTO POR MÊS ===\n\n";
    
    // 1. Buscar dados originais (mesma query do controller)
    $query = "SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
              FROM studies s 
              LEFT JOIN students st ON s.student_id = st.id 
              LEFT JOIN markets m ON s.market_id = m.id
              ORDER BY s.study_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "1. DADOS ORIGINAIS DA QUERY:\n";
    foreach ($studies as $study) {
        echo "   ID: {$study['id']} | Student: {$study['student_name']} | Date: {$study['study_date']} | Wins: {$study['wins']} | Losses: {$study['losses']}\n";
    }
    echo "\n";
    
    // 2. Simular a função problemática
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
            }
            
            // AQUI ESTÁ O PROBLEMA POTENCIAL
            $studiesByMonth[$monthYear]['studies'][] = $study;
            $studiesByMonth[$monthYear]['total_studies']++;
            $studiesByMonth[$monthYear]['total_wins'] += $study['wins'] ?? 0;
            $studiesByMonth[$monthYear]['total_losses'] += $study['losses'] ?? 0;
            $studiesByMonth[$monthYear]['total_profit_loss'] += $study['profit_loss'] ?? 0;
            
            echo "     -> Adicionado ao grupo. Total de estudos no grupo: {$studiesByMonth[$monthYear]['total_studies']}\n";
        }
        echo "\n";
    }
    
    // 3. Mostrar resultado final
    echo "3. RESULTADO FINAL DO AGRUPAMENTO:\n";
    foreach ($studiesByMonth as $monthYear => $monthData) {
        echo "   $monthYear ({$monthData['display']}):\n";
        echo "     Total de estudos: {$monthData['total_studies']}\n";
        echo "     Estudos no array:\n";
        
        foreach ($monthData['studies'] as $index => $study) {
            echo "       [$index] ID: {$study['id']} - {$study['student_name']} - {$study['study_date']}\n";
        }
        echo "\n";
    }
    
    // 4. Verificar duplicações específicas
    echo "4. VERIFICAÇÃO DE DUPLICAÇÕES:\n";
    foreach ($studiesByMonth as $monthYear => $monthData) {
        $studyIds = [];
        $duplicates = [];
        
        foreach ($monthData['studies'] as $study) {
            if (in_array($study['id'], $studyIds)) {
                $duplicates[] = $study['id'];
            } else {
                $studyIds[] = $study['id'];
            }
        }
        
        if (!empty($duplicates)) {
            echo "   DUPLICAÇÃO ENCONTRADA em $monthYear: IDs " . implode(', ', $duplicates) . "\n";
        } else {
            echo "   Nenhuma duplicação em $monthYear\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>