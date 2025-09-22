<?php
$pdo = new PDO('sqlite:backtest.db');

// Adicionar Luan Silva
$pdo->exec("INSERT INTO students (name, email, created, modified) VALUES ('Luan Silva', 'luan@example.com', datetime('now'), datetime('now'))");

// Pegar o ID do Luan
$luanId = $pdo->lastInsertId();
echo "Luan Silva adicionado com ID: $luanId\n";

// Adicionar estudos do Luan baseados nos dados mostrados pelo usuário
$studies = [
    [
        'student_id' => $luanId,
        'market_date' => '2025-01-05',
        'study_date' => '2025-01-06',
        'wins' => 5,
        'losses' => 1,
        'profit_loss' => 350.00,
        'notes' => 'WDO Futuro - Bom desempenho'
    ],
    [
        'student_id' => $luanId,
        'market_date' => '2025-01-02',
        'study_date' => '2025-01-03',
        'wins' => 8,
        'losses' => 1,
        'profit_loss' => 320.75,
        'notes' => 'WIN Futuro - Excelente performance'
    ]
];

foreach($studies as $study) {
    $stmt = $pdo->prepare("INSERT INTO studies (student_id, market_date, study_date, wins, losses, profit_loss, notes, created, modified) VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute([
        $study['student_id'],
        $study['market_date'],
        $study['study_date'],
        $study['wins'],
        $study['losses'],
        $study['profit_loss'],
        $study['notes']
    ]);
    echo "Estudo adicionado: {$study['study_date']} - Gain: {$study['wins']}, Loss: {$study['losses']}\n";
}

echo "\nVerificando estudos do Luan:\n";
$stmt = $pdo->prepare("SELECT * FROM studies WHERE student_id = ?");
$stmt->execute([$luanId]);
$luanStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($luanStudies as $study) {
    echo "ID: {$study['id']}, Data: {$study['study_date']}, Gain: {$study['wins']}, Loss: {$study['losses']}\n";
}
?>