-- Tabelas para o módulo de cursos

-- Tabela de cursos
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `category` varchar(100) DEFAULT NULL,
  `difficulty` enum('Iniciante','Intermediário','Avançado') DEFAULT 'Iniciante',
  `instructor` varchar(255) DEFAULT NULL,
  `thumbnail_url` varchar(500) DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `estimated_duration_hours` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order_position` int(11) DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_courses_active` (`is_active`),
  KEY `idx_courses_category` (`category`),
  KEY `idx_courses_difficulty` (`difficulty`),
  KEY `idx_courses_order` (`order_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de vídeos dos cursos
CREATE TABLE IF NOT EXISTS `course_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `video_url` varchar(500) NOT NULL,
  `video_type` enum('youtube','vimeo','direct') DEFAULT 'youtube',
  `duration_seconds` int(11) DEFAULT 0,
  `order_position` int(11) DEFAULT 0,
  `is_preview` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_course_videos_course` (`course_id`),
  KEY `idx_course_videos_active` (`is_active`),
  KEY `idx_course_videos_order` (`order_position`),
  CONSTRAINT `course_videos_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de inscrições nos cursos
CREATE TABLE IF NOT EXISTS `course_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`, `course_id`),
  KEY `idx_enrollments_student` (`student_id`),
  KEY `idx_enrollments_course` (`course_id`),
  CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de progresso dos estudantes
CREATE TABLE IF NOT EXISTS `student_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watch_time_seconds` int(11) DEFAULT 0,
  `watched_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_progress` (`student_id`, `course_id`, `video_id`),
  KEY `idx_progress_student` (`student_id`),
  KEY `idx_progress_course` (`course_id`),
  KEY `idx_progress_video` (`video_id`),
  CONSTRAINT `student_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_progress_ibfk_3` FOREIGN KEY (`video_id`) REFERENCES `course_videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados de exemplo
INSERT INTO `courses` (`title`, `description`, `category`, `difficulty`, `instructor`, `is_free`, `price`, `is_active`, `order_position`) VALUES
('Introdução ao Trading', 'Curso básico para iniciantes no mundo do trading', 'Trading Básico', 'Iniciante', 'João Silva', 1, NULL, 1, 1),
('Análise Técnica Avançada', 'Técnicas avançadas de análise técnica para traders experientes', 'Análise Técnica', 'Avançado', 'Maria Santos', 0, 299.90, 1, 2),
('Gestão de Risco', 'Como gerenciar riscos em suas operações', 'Risk Management', 'Intermediário', 'Pedro Costa', 1, NULL, 1, 3);

-- Inserir vídeos de exemplo para o primeiro curso
INSERT INTO `course_videos` (`course_id`, `title`, `description`, `video_url`, `video_type`, `duration_seconds`, `order_position`, `is_preview`, `is_active`) VALUES
(1, 'O que é Trading?', 'Introdução básica ao conceito de trading', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 600, 1, 1, 1),
(1, 'Tipos de Mercado', 'Conhecendo os diferentes tipos de mercado financeiro', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 900, 2, 0, 1),
(1, 'Plataformas de Trading', 'Como escolher e usar plataformas de trading', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 1200, 3, 0, 1);