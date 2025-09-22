<?php
// Script para adicionar estudantes de exemplo
try {
    $pdo = new PDO('mysql:host=localhost;dbname=backtest_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se já existem estudantes
    $stmt = $pdo->query("SELECT COUNT(*) FROM students");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "Adicionando estudantes de exemplo...\n";
        
        $students = [
            ['João Silva', 'joao@example.com'],
            ['Maria Santos', 'maria@example.com'],
            ['Pedro Oliveira', 'pedro@example.com'],
            ['Ana Costa', 'ana@example.com'],
            ['Carlos Ferreira', 'carlos@example.com']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO students (name, email, created, modified) VALUES (?, ?, NOW(), NOW())");
        
        foreach ($students as $student) {
            $stmt->execute($student);
            echo "Estudante adicionado: {$student[0]}\n";
        }
        
        echo "Estudantes de exemplo adicionados com sucesso!\n";
    } else {
        echo "Já existem {$count} estudantes na base de dados.\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>