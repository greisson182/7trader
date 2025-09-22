<?php
$pdo = new PDO('sqlite:backtest.db');

// Criar tabela markets
$pdo->exec("CREATE TABLE IF NOT EXISTS markets (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    name VARCHAR(100) NOT NULL, 
    code VARCHAR(20) NOT NULL UNIQUE, 
    currency VARCHAR(3) NOT NULL DEFAULT 'BRL', 
    active BOOLEAN NOT NULL DEFAULT 1
)");

// Inserir mercados
$pdo->exec("INSERT OR IGNORE INTO markets (name, code, currency) VALUES 
    ('WIN Futuro', 'WINFUT', 'BRL'), 
    ('WDO Futuro', 'WDOFUT', 'BRL')");

// Verificar se a coluna market_id já existe
$columns = $pdo->query("PRAGMA table_info(studies)")->fetchAll(PDO::FETCH_ASSOC);
$hasMarketId = false;
foreach($columns as $column) {
    if($column['name'] === 'market_id') {
        $hasMarketId = true;
        break;
    }
}

if(!$hasMarketId) {
    $pdo->exec("ALTER TABLE studies ADD COLUMN market_id INTEGER");
    echo "Coluna market_id adicionada\n";
}

// Atualizar estudos do Luan com os mercados corretos
$pdo->exec("UPDATE studies SET market_id = 1 WHERE id = 6"); // WIN Futuro
$pdo->exec("UPDATE studies SET market_id = 2 WHERE id = 5"); // WDO Futuro

echo "Tabela markets criada e estudos atualizados\n";

// Verificar resultado
$stmt = $pdo->query("SELECT s.*, st.name as student_name, m.name as market_name, m.currency 
    FROM studies s 
    LEFT JOIN students st ON s.student_id = st.id 
    LEFT JOIN markets m ON s.market_id = m.id
    WHERE st.name = 'Luan Silva'
    ORDER BY s.study_date DESC");

$studies = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nEstudos do Luan após configuração:\n";
foreach($studies as $study) {
    echo "ID: {$study['id']}, Data: {$study['study_date']}, Mercado: {$study['market_name']}, Gain: {$study['wins']}, Loss: {$study['losses']}\n";
}
?>