<?php
require_once 'config/bootstrap.php';

try {
    // Conectar ao banco de dados
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'backtest_db';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado ao banco de dados com sucesso!\n";
    
    // Criar tabela markets
    $createMarketsTable = "
        CREATE TABLE IF NOT EXISTS markets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL COMMENT 'Nome do mercado',
            code VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código do mercado',
            description TEXT COMMENT 'Descrição do mercado',
            active BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Se o mercado está ativo',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";
    
    $pdo->exec($createMarketsTable);
    echo "Tabela 'markets' criada com sucesso!\n";
    
    // Inserir mercados padrão
    $insertMarkets = "
        INSERT IGNORE INTO markets (name, code, description, active) VALUES 
        ('WIN Futuro', 'WINFUT', 'Contrato futuro do índice Bovespa', 1),
        ('WDO Futuro', 'WDOFUT', 'Contrato futuro de dólar comercial', 1)
    ";
    
    $pdo->exec($insertMarkets);
    echo "Mercados padrão inseridos com sucesso!\n";
    
    // Verificar se a coluna market_id já existe
    $checkColumn = "
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$database' 
        AND TABLE_NAME = 'studies' 
        AND COLUMN_NAME = 'market_id'
    ";
    
    $result = $pdo->query($checkColumn)->fetch();
    
    if ($result['count'] == 0) {
        // Adicionar coluna market_id na tabela studies
        $addMarketColumn = "
            ALTER TABLE studies 
            ADD COLUMN market_id INT NULL COMMENT 'ID do mercado associado ao estudo' AFTER student_id
        ";
        
        $pdo->exec($addMarketColumn);
        echo "Coluna 'market_id' adicionada à tabela 'studies'!\n";
        
        // Adicionar foreign key
        $addForeignKey = "
            ALTER TABLE studies 
            ADD CONSTRAINT fk_studies_market_id 
            FOREIGN KEY (market_id) REFERENCES markets(id) 
            ON DELETE SET NULL ON UPDATE CASCADE
        ";
        
        $pdo->exec($addForeignKey);
        echo "Foreign key adicionada com sucesso!\n";
    } else {
        echo "Coluna 'market_id' já existe na tabela 'studies'!\n";
    }
    
    // Definir WINFUT como padrão para estudos existentes
    $updateExistingStudies = "
        UPDATE studies 
        SET market_id = (SELECT id FROM markets WHERE code = 'WINFUT' LIMIT 1) 
        WHERE market_id IS NULL
    ";
    
    $pdo->exec($updateExistingStudies);
    echo "Estudos existentes atualizados com mercado padrão (WINFUT)!\n";
    
    // Verificar os mercados criados
    $markets = $pdo->query("SELECT * FROM markets")->fetchAll();
    echo "\nMercados disponíveis:\n";
    foreach ($markets as $market) {
        echo "- ID: {$market['id']}, Código: {$market['code']}, Nome: {$market['name']}\n";
    }
    
    echo "\nTodas as alterações foram aplicadas com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>