<?php
$pdo = new PDO('sqlite:backtest.db');
$stmt = $pdo->prepare('SELECT * FROM studies WHERE id = 3');
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Registros encontrados para ID 3: " . count($results) . PHP_EOL;
foreach($results as $result) {
    print_r($result);
}

// Verificar todos os estudos do Luan
$stmt2 = $pdo->prepare('SELECT s.*, st.name as student_name FROM studies s LEFT JOIN students st ON s.student_id = st.id WHERE st.name = "Luan Silva" ORDER BY s.id');
$stmt2->execute();
$luan_studies = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "\n\nTodos os estudos do Luan Silva:\n";
foreach($luan_studies as $study) {
    echo "ID: {$study['id']}, Data: {$study['study_date']}, Gain: {$study['gain']}, Loss: {$study['loss']}\n";
}
?>