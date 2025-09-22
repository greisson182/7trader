-- Criar tabela de mercados
CREATE TABLE IF NOT EXISTS markets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nome do mercado',
    code VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código do mercado',
    description TEXT COMMENT 'Descrição do mercado',
    active BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Se o mercado está ativo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir os mercados padrão
INSERT INTO markets (name, code, description, active) VALUES 
('WIN Futuro', 'WINFUT', 'Contrato futuro do índice Bovespa', 1),
('WDO Futuro', 'WDOFUT', 'Contrato futuro de dólar comercial', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Adicionar campo market_id na tabela studies se não existir
ALTER TABLE studies 
ADD COLUMN IF NOT EXISTS market_id INT NULL COMMENT 'ID do mercado associado ao estudo' AFTER student_id;

-- Adicionar foreign key se não existir
ALTER TABLE studies 
ADD CONSTRAINT fk_studies_market_id 
FOREIGN KEY (market_id) REFERENCES markets(id) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Definir WINFUT como padrão para estudos existentes
UPDATE studies 
SET market_id = (SELECT id FROM markets WHERE code = 'WINFUT' LIMIT 1) 
WHERE market_id IS NULL;