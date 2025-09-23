<?php
declare(strict_types=1);

namespace App\Controller\Site;

use App\Controller\AppController;
use PDO;
use Exception;

/**
 * Courses Controller for Students
 *
 * @property \App\Model\Table\CoursesTable $Courses
 */
class CoursesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Verificar se usuário está logado
        if (!$this->isLoggedIn()) {
            $this->flash('Você precisa estar logado para acessar os cursos.', 'error');
            $this->redirect('/auth/login');
            return;
        }
    }

    /**
     * Lista todos os cursos disponíveis para estudantes
     */
    public function index()
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar cursos ativos
            $stmt = $pdo->prepare("
                SELECT c.*, 
                       COUNT(cv.id) as video_count,
                       SUM(cv.duration_seconds) as total_duration
                FROM courses c
                LEFT JOIN course_videos cv ON c.id = cv.course_id AND cv.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.order_position ASC, c.created DESC
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Se for estudante, verificar inscrições
            $enrollments = [];
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                $stmt = $pdo->prepare("
                    SELECT course_id, enrolled_at, completed_at 
                    FROM course_enrollments 
                    WHERE student_id = ?
                ");
                $stmt->execute([$studentId]);
                $enrollmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($enrollmentData as $enrollment) {
                    $enrollments[$enrollment['course_id']] = $enrollment;
                }
            }

            $this->set('courses', $courses);
            $this->set('enrollments', $enrollments);
            return $this->render('Site/Courses/index');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar cursos: ' . $e->getMessage(), 'error');
            return $this->redirect('/');
        }
    }

    /**
     * Visualizar detalhes de um curso
     */
    public function view($courseId = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar curso
            $stmt = $pdo->prepare("
                SELECT c.*, 
                       COUNT(cv.id) as video_count,
                       SUM(cv.duration_seconds) as total_duration
                FROM courses c
                LEFT JOIN course_videos cv ON c.id = cv.course_id AND cv.is_active = 1
                WHERE c.id = ? AND c.is_active = 1
                GROUP BY c.id
            ");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            // Verificar se estudante está inscrito
            $isEnrolled = false;
            $enrollment = null;
            $progress = [];
            
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                
                // Verificar inscrição
                $stmt = $pdo->prepare("
                    SELECT * FROM course_enrollments 
                    WHERE student_id = ? AND course_id = ?
                ");
                $stmt->execute([$studentId, $courseId]);
                $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
                $isEnrolled = (bool)$enrollment;

                // Se inscrito, buscar progresso
                if ($isEnrolled) {
                    $stmt = $pdo->prepare("
                        SELECT video_id, watched_at, completed_at, watch_time_seconds
                        FROM student_progress 
                        WHERE student_id = ? AND course_id = ?
                    ");
                    $stmt->execute([$studentId, $courseId]);
                    $progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($progressData as $p) {
                        $progress[$p['video_id']] = $p;
                    }
                }
            }

            // Buscar vídeos do curso
            $videoQuery = "
                SELECT * FROM course_videos 
                WHERE course_id = ? AND is_active = 1
            ";
            
            // Se não inscrito e curso pago, mostrar apenas previews
            if (!$isEnrolled && !$course['is_free']) {
                $videoQuery .= " AND is_preview = 1";
            }
            
            $videoQuery .= " ORDER BY order_position ASC, created ASC";
            
            $stmt = $pdo->prepare($videoQuery);
            $stmt->execute([$courseId]);
            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->set('course', $course);
            $this->set('videos', $videos);
            $this->set('isEnrolled', $isEnrolled);
            $this->set('enrollment', $enrollment);
            $this->set('progress', $progress);
            return $this->render('Site/Courses/view');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar curso: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Assistir um vídeo específico do curso
     */
    public function watch($courseId = null, $videoId = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se é estudante
            if (!$this->isStudent()) {
                $this->flash('Apenas estudantes podem assistir vídeos.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            $studentId = $this->getCurrentStudentId();

            // Buscar curso
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            // Buscar vídeo
            $stmt = $pdo->prepare("
                SELECT * FROM course_videos 
                WHERE id = ? AND course_id = ? AND is_active = 1
            ");
            $stmt->execute([$videoId, $courseId]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$video) {
                $this->flash('Vídeo não encontrado.', 'error');
                return $this->redirect(['action' => 'view', $courseId]);
            }

            // Verificar permissão para assistir
            $canWatch = false;
            
            // Curso gratuito ou vídeo de preview
            if ($course['is_free'] || $video['is_preview']) {
                $canWatch = true;
            } else {
                // Verificar se está inscrito
                $stmt = $pdo->prepare("
                    SELECT * FROM course_enrollments 
                    WHERE student_id = ? AND course_id = ?
                ");
                $stmt->execute([$studentId, $courseId]);
                $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
                $canWatch = (bool)$enrollment;
            }

            if (!$canWatch) {
                $this->flash('Você precisa se inscrever neste curso para assistir este vídeo.', 'error');
                return $this->redirect(['action' => 'view', $courseId]);
            }

            // Buscar todos os vídeos do curso para navegação
            $stmt = $pdo->prepare("
                SELECT id, title, order_position, is_preview, duration_seconds
                FROM course_videos 
                WHERE course_id = ? AND is_active = 1
                ORDER BY order_position ASC, created ASC
            ");
            $stmt->execute([$courseId]);
            $allVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Buscar progresso do estudante
            $stmt = $pdo->prepare("
                SELECT * FROM student_progress 
                WHERE student_id = ? AND course_id = ? AND video_id = ?
            ");
            $stmt->execute([$studentId, $courseId, $videoId]);
            $progress = $stmt->fetch(PDO::FETCH_ASSOC);

            // Registrar que começou a assistir (se ainda não registrou)
            if (!$progress) {
                $stmt = $pdo->prepare("
                    INSERT INTO student_progress (student_id, course_id, video_id, watched_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([$studentId, $courseId, $videoId]);
            } else {
                // Atualizar última visualização
                $stmt = $pdo->prepare("
                    UPDATE student_progress 
                    SET watched_at = NOW() 
                    WHERE student_id = ? AND course_id = ? AND video_id = ?
                ");
                $stmt->execute([$studentId, $courseId, $videoId]);
            }

            $this->set('course', $course);
            $this->set('video', $video);
            $this->set('allVideos', $allVideos);
            $this->set('progress', $progress);
            return $this->render('Site/Courses/watch');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar vídeo: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'view', $courseId]);
        }
    }

    /**
     * Inscrever-se em um curso
     */
    public function enroll($courseId = null)
    {
        try {
            if (!$this->isStudent()) {
                $this->flash('Apenas estudantes podem se inscrever em cursos.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            $pdo = $this->getDbConnection();
            $studentId = $this->getCurrentStudentId();

            // Verificar se curso existe e está ativo
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            // Verificar se já está inscrito
            $stmt = $pdo->prepare("
                SELECT * FROM course_enrollments 
                WHERE student_id = ? AND course_id = ?
            ");
            $stmt->execute([$studentId, $courseId]);
            $existingEnrollment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingEnrollment) {
                $this->flash('Você já está inscrito neste curso.', 'info');
                return $this->redirect(['action' => 'view', $courseId]);
            }

            // Para cursos pagos, aqui seria implementada a lógica de pagamento
            if (!$course['is_free']) {
                $this->flash('Funcionalidade de pagamento ainda não implementada. Entre em contato com o suporte.', 'info');
                return $this->redirect(['action' => 'view', $courseId]);
            }

            // Inscrever no curso
            $stmt = $pdo->prepare("
                INSERT INTO course_enrollments (student_id, course_id, enrolled_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$studentId, $courseId]);

            $this->flash('Inscrição realizada com sucesso! Agora você pode assistir aos vídeos.', 'success');
            return $this->redirect(['action' => 'view', $courseId]);
        } catch (Exception $e) {
            $this->flash('Erro ao realizar inscrição: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'view', $courseId]);
        }
    }

    /**
     * Atualizar progresso do vídeo (AJAX)
     */
    public function updateProgress()
    {
        $this->autoRender = false;
        
        if (!$this->request->is('post') || !$this->isStudent()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $videoId = $data['video_id'] ?? null;
            $courseId = $data['course_id'] ?? null;
            $watchTime = $data['watch_time'] ?? 0;
            $completed = $data['completed'] ?? false;

            if (!$videoId || !$courseId) {
                http_response_code(400);
                echo json_encode(['error' => 'Dados inválidos']);
                return;
            }

            $pdo = $this->getDbConnection();
            $studentId = $this->getCurrentStudentId();

            // Atualizar progresso
            $stmt = $pdo->prepare("
                INSERT INTO student_progress (student_id, course_id, video_id, watch_time_seconds, completed_at, watched_at)
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                watch_time_seconds = VALUES(watch_time_seconds),
                completed_at = CASE WHEN ? THEN NOW() ELSE completed_at END,
                watched_at = NOW()
            ");
            $stmt->execute([
                $studentId, 
                $courseId, 
                $videoId, 
                $watchTime, 
                $completed ? date('Y-m-d H:i:s') : null,
                $completed
            ]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}