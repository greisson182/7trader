<?php
$pdo = new PDO('sqlite:backtest.db');
$stmt = $pdo->query('SELECT * FROM students');
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Estudantes no banco:\n";
foreach($students as $student) {
    echo "ID: {$student['id']}, Nome: {$student['name']}\n";
}

// Verificar se existe Luan Silva
$stmt2 = $pdo->prepare('SELECT * FROM students WHERE name LIKE ?');
$stmt2->execute(['%Luan%']);
$luanStudents = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "\nEstudantes com 'Luan' no nome:\n";
foreach($luanStudents as $student) {
    echo "ID: {$student['id']}, Nome: {$student['name']}\n";
}

// Verificar todos os estudos
$stmt3 = $pdo->query('SELECT * FROM studies');
$studies = $stmt3->fetchAll(PDO::FETCH_ASSOC);

echo "\nTodos os estudos no banco:\n";
foreach($studies as $study) {
    echo "ID: {$study['id']}, Student ID: {$study['student_id']}, Data: {$study['study_date']}\n";
}
?>