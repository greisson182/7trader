<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db', 'root', '');
    $stmt = $pdo->query('SELECT id, name FROM students ORDER BY name');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Estudantes na base de dados:\n";
    foreach($students as $student) {
        echo "- ID: {$student['id']}, Nome: {$student['name']}\n";
    }
    
    echo "\nTotal: " . count($students) . " estudantes\n";
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>