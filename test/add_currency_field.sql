-- Add currency field to users table
USE backtest_db;

ALTER TABLE users ADD COLUMN currency ENUM('BRL', 'USD') NOT NULL DEFAULT 'BRL' COMMENT 'User preferred currency: BRL or USD' AFTER active;

-- Update existing users to have BRL as default currency
UPDATE users SET currency = 'BRL' WHERE currency IS NULL;

-- Show the updated table structure
DESCRIBE users;