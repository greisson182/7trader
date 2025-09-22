<?php
// Simular o que o controller faz sem a tabela markets
$pdo = new PDO('sqlite:backtest.db');

// Query simplificada sem markets
$stmt = $pdo->query("
    SELECT s.*, st.name as student_name
    FROM studies s 
    LEFT JOIN students st ON s.student_id = st.id 
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

// Verificar se há duplicação na query base
echo "\n\nVerificando duplicação na query base:\n";
$duplicateCheck = [];
foreach($studies as $study) {
    $key = $study['id'];
    if(isset($duplicateCheck[$key])) {
        echo "DUPLICAÇÃO ENCONTRADA! ID {$key} aparece múltiplas vezes\n";
    }
    $duplicateCheck[$key] = true;
}

// Verificar se o problema está no agrupamento
echo "\n\nSimulando agrupamento por mês:\n";
$studiesByMonth = [];
foreach ($studies as $study) {
    if (!empty($study['study_date'])) {
        $date = new DateTime($study['study_date']);
        $monthYear = $date->format('Y-m');
        
        if (!isset($studiesByMonth[$monthYear])) {
            $studiesByMonth[$monthYear] = [
                'studies' => []
            ];
        }
        
        $studiesByMonth[$monthYear]['studies'][] = $study;
    }
}

foreach($studiesByMonth as $monthYear => $monthData) {
    echo "\nMês: {$monthYear}\n";
    $luanCount = 0;
    foreach($monthData['studies'] as $study) {
        if($study['student_name'] === 'Luan Silva') {
            $luanCount++;
            echo "  - ID: {$study['id']}, Data: {$study['study_date']}\n";
        }
    }
    echo "Total estudos Luan neste mês: {$luanCount}\n";
}
?>