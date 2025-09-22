-- Create the database tables for Market Replay Tracker

USE backtest_db;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
);

-- Create studies table
CREATE TABLE IF NOT EXISTS studies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    market_date DATE NOT NULL COMMENT 'The date of the market being studied',
    study_date DATE NOT NULL COMMENT 'The date when the study was conducted',
    wins INT NOT NULL DEFAULT 0 COMMENT 'Number of winning trades',
    losses INT NOT NULL DEFAULT 0 COMMENT 'Number of losing trades',
    profit_loss DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total profit/loss amount',
    notes TEXT COMMENT 'Additional notes about the study',
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert some sample data
INSERT INTO students (name, email, created, modified) VALUES 
('John Doe', 'john@example.com', NOW(), NOW()),
('Jane Smith', 'jane@example.com', NOW(), NOW()),
('Mike Johnson', 'mike@example.com', NOW(), NOW());

INSERT INTO studies (student_id, market_date, study_date, wins, losses, profit_loss, notes, created, modified) VALUES
(1, '2024-01-15', '2024-01-16', 5, 2, 150.50, 'Good performance on trending day', NOW(), NOW()),
(1, '2024-01-16', '2024-01-17', 3, 4, -75.25, 'Struggled with choppy market', NOW(), NOW()),
(2, '2024-01-15', '2024-01-16', 8, 1, 320.75, 'Excellent risk management', NOW(), NOW()),
(3, '2024-01-17', '2024-01-18', 4, 3, 45.00, 'Steady improvement', NOW(), NOW());