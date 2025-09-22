<?php
// Simular o que o controller faz
$pdo = new PDO('sqlite:backtest.db');

// Executar a mesma query do controller (admin)
$stmt = $pdo->query("
    SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
    FROM studies s 
    LEFT JOIN students st ON s.student_id = st.id 
    LEFT JOIN markets m ON s.market_id = m.id
    ORDER BY s.study_date DESC
");

$studies = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total de estudos retornados pela query: " . count($studies) . "\n\n";

// Mostrar apenas estudos do Luan
$luanStudies = array_filter($studies, function($study) {
    return $study['student_name'] === 'Luan Silva';
});

echo "Estudos do Luan Silva encontrados: " . count($luanStudies) . "\n";
foreach($luanStudies as $study) {
    echo "ID: {$study['id']}, Data: {$study['study_date']}, Gain: {$study['wins']}, Loss: {$study['losses']}, P&L: {$study['profit_loss']}\n";
}

// Adicionar dados do usuário como no controller
foreach ($studies as &$study) {
    $study['user'] = [
        'currency' => $study['currency'] ?? 'BRL'
    ];
}

// Agrupar por mês como no controller
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

krsort($studiesByMonth);

echo "\n\nEstudos agrupados por mês:\n";
foreach($studiesByMonth as $monthYear => $monthData) {
    echo "\nMês: {$monthData['display']}\n";
    echo "Total de estudos: {$monthData['total_studies']}\n";
    foreach($monthData['studies'] as $study) {
        if($study['student_name'] === 'Luan Silva') {
            echo "  - ID: {$study['id']}, Data: {$study['study_date']}, Gain: {$study['wins']}, Loss: {$study['losses']}\n";
        }
    }
}
?>