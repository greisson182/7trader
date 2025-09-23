-- --------------------------------------------------------
-- Servidor:                     localhost
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando estrutura para tabela backtest_db.accounts
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_accounts_active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela backtest_db.accounts: ~2 rows (aproximadamente)
INSERT INTO `accounts` (`id`, `name`, `active`, `created`, `modified`) VALUES
	(1, 'Simulador', 1, '2025-09-23 12:49:14', '2025-09-23 13:00:32'),
	(2, 'Real', 1, '2025-09-23 12:49:14', '2025-09-23 13:00:37');

-- Copiando estrutura para tabela backtest_db.markets
DROP TABLE IF EXISTS `markets`;
CREATE TABLE IF NOT EXISTS `markets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(50) NOT NULL DEFAULT 'forex',
  `currency` varchar(3) NOT NULL DEFAULT 'BRL',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela backtest_db.markets: ~2 rows (aproximadamente)
INSERT INTO `markets` (`id`, `name`, `code`, `description`, `active`, `type`, `currency`, `created_at`, `updated_at`) VALUES
	(1, 'Índice Futuro', 'WINFUT', '', 1, 'indice', 'BRL', '2025-09-23 12:49:14', '2025-09-23 13:00:59'),
	(2, 'Dólar Futuro', 'WDOFUT', '', 1, 'indice', 'BRL', '2025-09-23 12:49:14', '2025-09-23 13:01:02');

-- Copiando estrutura para tabela backtest_db.students
DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela backtest_db.students: ~2 rows (aproximadamente)
INSERT INTO `students` (`id`, `name`, `email`, `phone`, `created`, `modified`) VALUES
	(1, 'Greisson Silva', 'greisson182@gmail.com', '(41) 99275-2998', '2025-09-23 12:49:14', '2025-09-23 13:03:19'),
	(2, 'Luan Silva', 'luanlp@gmail.com', NULL, '2025-09-23 13:13:50', '2025-09-23 13:13:58');

-- Copiando estrutura para tabela backtest_db.studies
DROP TABLE IF EXISTS `studies`;
CREATE TABLE IF NOT EXISTS `studies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `market_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `study_date` date NOT NULL,
  `wins` int(11) NOT NULL DEFAULT 0,
  `losses` int(11) NOT NULL DEFAULT 0,
  `profit_loss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_studies_student` (`student_id`),
  KEY `idx_studies_market` (`market_id`),
  KEY `idx_studies_account` (`account_id`),
  KEY `idx_studies_dates` (`study_date`),
  CONSTRAINT `studies_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `studies_ibfk_2` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `studies_ibfk_3` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela backtest_db.studies: ~2 rows (aproximadamente)
INSERT INTO `studies` (`id`, `student_id`, `market_id`, `account_id`, `study_date`, `wins`, `losses`, `profit_loss`, `notes`, `created`, `modified`) VALUES
	(1, 1, 1, NULL, '2025-01-02', 7, 2, 205.00, '', '2025-09-23 13:13:10', '2025-09-23 13:13:10'),
	(2, 2, 2, NULL, '2025-02-03', 5, 2, 305.00, '', '2025-09-23 13:16:19', '2025-09-23 13:16:19');

-- Copiando estrutura para tabela backtest_db.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `student_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela backtest_db.users: ~3 rows (aproximadamente)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `student_id`, `active`, `created`, `modified`) VALUES
	(1, 'admin', 'admin@backtest.com', '$2y$10$E3belYzSnodJDLc1zZ5s.OdyqLyNwzsxdaFIngjPPpzSJlSIAyuz2', 'admin', NULL, 1, '2025-09-23 12:49:14', '2025-09-23 13:17:17'),
	(2, 'greisson', 'greisson182@gmail.com', '$2y$10$E3belYzSnodJDLc1zZ5s.OdyqLyNwzsxdaFIngjPPpzSJlSIAyuz2', 'student', 1, 1, '2025-09-23 12:56:38', '2025-09-23 13:03:19'),
	(3, 'luan', 'luanlp@gmail.com', '$2y$10$E3belYzSnodJDLc1zZ5s.OdyqLyNwzsxdaFIngjPPpzSJlSIAyuz2', 'student', 2, 1, '2025-09-23 13:14:15', '2025-09-23 13:14:27');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
