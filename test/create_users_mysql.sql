-- Create users table for MySQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL COMMENT 'Username for login',
    email VARCHAR(100) NOT NULL COMMENT 'User email address',
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student' COMMENT 'User role: admin or student',
    student_id INT NULL COMMENT 'Reference to students table if role is student',
    active BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Whether the user account is active',
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email),
    KEY idx_student_id (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT INTO users (username, email, password, role, active, created, modified) 
VALUES (
    'admin', 
    'admin@example.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin', 
    TRUE,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    modified = NOW();