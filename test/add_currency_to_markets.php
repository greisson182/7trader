<?php
require_once 'config/bootstrap.php';

// Database connection function
function getDbConnection() {
    global $app_config;
    
    // Default database configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'backtest_db';
    
    // Try to get config from app_config if available
    if (isset($app_config['Datasources']['default'])) {
        $config = $app_config['Datasources']['default'];
        
        // Ensure we get string values, not arrays
        $host = is_string($config['host']) ? $config['host'] : $host;
        $username = is_string($config['username']) ? $config['username'] : $username;
        $password = is_string($config['password']) ? $config['password'] : $password;
        $database = is_string($config['database']) ? $config['database'] : $database;
    }
    
    try {
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Fallback to SQLite if MySQL fails
        try {
            $pdo = new PDO('sqlite:' . ROOT . '/backtest.db');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e2) {
            throw new Exception("Database connection failed: " . $e2->getMessage());
        }
    }
}

try {
    $pdo = getDbConnection();
    
    // Verificar se a coluna já existe (MySQL/MariaDB)
    $stmt = $pdo->query("SHOW COLUMNS FROM markets LIKE 'currency'");
    $currencyExists = $stmt->rowCount() > 0;
    
    if (!$currencyExists) {
        // Adicionar a coluna currency
        $pdo->exec("ALTER TABLE markets ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'BRL'");
        echo "✅ Campo 'currency' adicionado à tabela 'markets' com sucesso!\n";
        
        // Atualizar mercados existentes com moedas apropriadas
        $updates = [
            ['code' => 'WINFUT', 'currency' => 'BRL'],
            ['code' => 'WINM', 'currency' => 'BRL'],
            ['code' => 'ES', 'currency' => 'USD'],
            ['code' => 'NQ', 'currency' => 'USD'],
            ['code' => 'YM', 'currency' => 'USD'],
            ['code' => 'EURUSD', 'currency' => 'EUR'],
            ['code' => 'GBPUSD', 'currency' => 'USD'],
        ];
        
        foreach ($updates as $update) {
            $stmt = $pdo->prepare("UPDATE markets SET currency = ? WHERE code = ?");
            $stmt->execute([$update['currency'], $update['code']]);
        }
        
        echo "✅ Moedas dos mercados existentes atualizadas!\n";
    } else {
        echo "ℹ️ Campo 'currency' já existe na tabela 'markets'.\n";
    }
    
    // Mostrar estrutura atual da tabela
    echo "\n📋 Estrutura atual da tabela 'markets':\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM markets");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) " . 
             ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
             ($column['Default'] ? " DEFAULT {$column['Default']}" : '') . "\n";
    }
    
    // Mostrar mercados com suas moedas
    echo "\n💰 Mercados e suas moedas:\n";
    $stmt = $pdo->query("SELECT name, code, currency FROM markets ORDER BY name");
    $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($markets as $market) {
        echo "- {$market['name']} ({$market['code']}) - {$market['currency']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>