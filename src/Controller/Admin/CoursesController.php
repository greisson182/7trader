<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use PDO;
use Exception;

class CoursesController extends AppController
{
    public function index()
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar todos os cursos com contagem de vídeos
            $stmt = $pdo->query("
                SELECT c.*, 
                       COUNT(cv.id) as video_count,
                       SUM(cv.duration_seconds) as total_duration
                FROM courses c 
                LEFT JOIN course_videos cv ON c.id = cv.course_id AND cv.is_active = 1
                GROUP BY c.id 
                ORDER BY c.order_position ASC, c.created DESC
            ");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set('courses', $courses);
            return $this->render('Admin/Courses/index');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar cursos: ' . $e->getMessage(), 'error');
            return $this->render('Admin/Courses/index');
        }
    }

    public function view($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar curso
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Buscar vídeos do curso
            $stmt = $pdo->prepare("
                SELECT * FROM course_videos 
                WHERE course_id = ? AND is_active = 1 
                ORDER BY order_position ASC
            ");
            $stmt->execute([$id]);
            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar estatísticas de inscrições
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total_enrollments,
                       COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed_enrollments
                FROM course_enrollments 
                WHERE course_id = ? AND is_active = 1
            ");
            $stmt->execute([$id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->set('course', $course);
            $this->set('videos', $videos);
            $this->set('stats', $stats);
            return $this->render('Admin/Courses/view');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar curso: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = $this->getDbConnection();
                $data = $_POST;
                
                // Validações básicas
                if (empty($data['title'])) {
                    $this->flash('Título é obrigatório.', 'error');
                    return $this->render('Admin/Courses/add');
                }
                
                // Buscar próxima posição
                $stmt = $pdo->query("SELECT MAX(order_position) as max_pos FROM courses");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextPosition = ($result['max_pos'] ?? 0) + 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO courses (title, description, thumbnail, duration_minutes, difficulty, 
                                       category, instructor, price, is_free, is_active, order_position)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $data['title'],
                    $data['description'] ?? '',
                    $data['thumbnail'] ?? '',
                    (int)($data['duration_minutes'] ?? 0),
                    $data['difficulty'] ?? 'Iniciante',
                    $data['category'] ?? '',
                    $data['instructor'] ?? '',
                    (float)($data['price'] ?? 0.00),
                    isset($data['is_free']) ? 1 : 0,
                    isset($data['is_active']) ? 1 : 0,
                    $nextPosition
                ]);
                
                $this->flash('Curso criado com sucesso!', 'success');
                return $this->redirect(['action' => 'index']);
            } catch (Exception $e) {
                $this->flash('Erro ao criar curso: ' . $e->getMessage(), 'error');
            }
        }
        
        return $this->render('Admin/Courses/add');
    }

    public function edit($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar curso
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $_POST;
                
                // Validações básicas
                if (empty($data['title'])) {
                    $this->flash('Título é obrigatório.', 'error');
                    return $this->render('Admin/Courses/edit');
                }
                
                $stmt = $pdo->prepare("
                    UPDATE courses SET 
                        title = ?, description = ?, thumbnail = ?, duration_minutes = ?, 
                        difficulty = ?, category = ?, instructor = ?, price = ?, 
                        is_free = ?, is_active = ?, modified = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $data['title'],
                    $data['description'] ?? '',
                    $data['thumbnail'] ?? '',
                    (int)($data['duration_minutes'] ?? 0),
                    $data['difficulty'] ?? 'Iniciante',
                    $data['category'] ?? '',
                    $data['instructor'] ?? '',
                    (float)($data['price'] ?? 0.00),
                    isset($data['is_free']) ? 1 : 0,
                    isset($data['is_active']) ? 1 : 0,
                    $id
                ]);
                
                $this->flash('Curso atualizado com sucesso!', 'success');
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->set('course', $course);
            return $this->render('Admin/Courses/edit');
        } catch (Exception $e) {
            $this->flash('Erro ao editar curso: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se curso existe
            $stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Verificar se há inscrições ativas
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM course_enrollments WHERE course_id = ? AND is_active = 1");
            $stmt->execute([$id]);
            $enrollments = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($enrollments['count'] > 0) {
                $this->flash('Não é possível excluir curso com inscrições ativas. Desative o curso ao invés de excluí-lo.', 'error');
                return $this->redirect(['action' => 'view', $id]);
            }
            
            // Excluir curso (cascade irá excluir vídeos e progresso)
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->flash("Curso '{$course['title']}' excluído com sucesso!", 'success');
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->flash('Erro ao excluir curso: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    // Gerenciar vídeos do curso
    public function videos($courseId = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se curso existe
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Buscar vídeos do curso
            $stmt = $pdo->prepare("
                SELECT * FROM course_videos 
                WHERE course_id = ? 
                ORDER BY order_position ASC, created ASC
            ");
            $stmt->execute([$courseId]);
            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set('course', $course);
            $this->set('videos', $videos);
            return $this->render('Admin/Courses/videos');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar vídeos: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function addVideo($courseId = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se curso existe
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $_POST;
                
                // Validações básicas
                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->flash('Título e URL do vídeo são obrigatórios.', 'error');
                    return $this->render('Admin/Courses/add_video');
                }
                
                // Buscar próxima posição
                $stmt = $pdo->prepare("SELECT MAX(order_position) as max_pos FROM course_videos WHERE course_id = ?");
                $stmt->execute([$courseId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextPosition = ($result['max_pos'] ?? 0) + 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO course_videos (course_id, title, description, video_url, video_type, 
                                             duration_seconds, order_position, is_preview, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $courseId,
                    $data['title'],
                    $data['description'] ?? '',
                    $data['video_url'],
                    $data['video_type'] ?? 'youtube',
                    (int)($data['duration_seconds'] ?? 0),
                    $nextPosition,
                    isset($data['is_preview']) ? 1 : 0,
                    isset($data['is_active']) ? 1 : 0
                ]);
                
                $this->flash('Vídeo adicionado com sucesso!', 'success');
                return $this->redirect(['action' => 'videos', $courseId]);
            }
            
            $this->set('course', $course);
            return $this->render('Admin/Courses/add_video');
        } catch (Exception $e) {
            $this->flash('Erro ao adicionar vídeo: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'videos', $courseId]);
        }
    }

    public function editVideo($videoId = null)
    {
        if (!$videoId) {
            $this->flash('ID do vídeo é obrigatório.', 'error');
            return $this->redirect(['action' => 'index']);
        }

        $pdo = $this->getDbConnection();
        
        // Buscar o vídeo
        $stmt = $pdo->prepare("SELECT * FROM course_videos WHERE id = ?");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$video) {
            $this->flash('Vídeo não encontrado.', 'error');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            
            if (empty($data['title']) || empty($data['video_url'])) {
                $this->flash('Título e URL do vídeo são obrigatórios.', 'error');
                $this->set('video', $video);
                return $this->render('Admin/Courses/edit_video');
            }

            try {
                // Converter minutos para segundos se fornecido
                $durationSeconds = null;
                if (!empty($data['duration_minutes']) && is_numeric($data['duration_minutes'])) {
                    $durationSeconds = (int)$data['duration_minutes'] * 60;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE course_videos 
                    SET title = ?, description = ?, video_url = ?, video_type = ?, 
                        duration_seconds = ?, is_active = ?, modified = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $data['title'],
                    $data['description'] ?? '',
                    $data['video_url'],
                    $data['video_type'] ?? 'youtube',
                    $durationSeconds,
                    isset($data['is_active']) ? 1 : 0,
                    $videoId
                ]);

                $this->flash('Vídeo atualizado com sucesso!', 'success');
                return $this->redirect(['action' => 'videos', $video['course_id']]);
                
            } catch (Exception $e) {
                $this->flash('Erro ao atualizar vídeo: ' . $e->getMessage(), 'error');
            }
        }

        $this->set('video', $video);
        return $this->render('Admin/Courses/edit_video');
    }

    public function watchVideo($videoId = null)
    {
        if (!$videoId) {
            $this->flash('ID do vídeo não fornecido.', 'error');
            return $this->redirect(['action' => 'index']);
        }

        $db = $this->getDbConnection();
        
        try {
            // Buscar dados do vídeo
            $stmt = $db->prepare("
                SELECT cv.*, c.title as course_title 
                FROM course_videos cv 
                LEFT JOIN courses c ON cv.course_id = c.id 
                WHERE cv.id = ? AND cv.is_active = 1
            ");
            $stmt->execute([$videoId]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$video) {
                $this->flash('Vídeo não encontrado ou inativo.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            $this->set('video', $video);
            return $this->render('Admin/Courses/watch_video');
            
        } catch (Exception $e) {
            $this->flash('Erro ao carregar vídeo: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

        /**
     * Lista todos os cursos disponíveis para estudantes
     */
    public function indexStudents()
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
            return $this->render('Admin/Courses/index_students');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar cursos: ' . $e->getMessage(), 'error');
            return $this->redirect('/');
        }
    }

    /**
     * Visualizar detalhes de um curso
     */
    public function viewStudents($courseId = null)
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
                return $this->redirect(['action' => 'index-students']);
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
            return $this->render('Admin/Courses/view_students');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar curso: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index-students']);
        }
    }

    /**
     * Assistir um vídeo específico do curso
     */
    public function watchStudents($courseId = null, $videoId = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se é estudante
            if (!$this->isStudent()) {
                $this->flash('Apenas estudantes podem assistir vídeos.', 'error');
                return $this->redirect(['action' => 'index-students']);
            }

            $studentId = $this->getCurrentStudentId();

            // Buscar curso
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index-students']);
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
                return $this->redirect(['action' => 'view-students', $courseId]);
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
                return $this->redirect(['action' => 'view-students', $courseId]);
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
            return $this->render('Admin/Courses/watch_students');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar vídeo: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'view-students', $courseId]);
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
                return $this->redirect(['action' => 'index-students']);
            }

            $pdo = $this->getDbConnection();
            $studentId = $this->getCurrentStudentId();

            // Verificar se curso existe e está ativo
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index-students']);
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
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Para cursos pagos, redirecionar para página de pagamento
            if (!$course['is_free']) {
                $this->flash('Redirecionando para o pagamento...', 'info');
                return $this->redirect(['action' => 'purchase-students', $courseId]);
            }

            // Inscrever no curso
            $stmt = $pdo->prepare("
                INSERT INTO course_enrollments (student_id, course_id, enrolled_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$studentId, $courseId]);

            $this->flash('Inscrição realizada com sucesso! Agora você pode assistir aos vídeos.', 'success');
            return $this->redirect(['action' => 'view-students', $courseId]);
        } catch (Exception $e) {
            $this->flash('Erro ao realizar inscrição: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'view-students', $courseId]);
        }
    }

    /**
     * Atualizar progresso do vídeo
     */
    public function updateProgress()
    {
        if (!$this->request->is('post')) {
            return $this->response->withStatus(405);
        }

        try {
            if (!$this->isStudent()) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Acesso negado']));
            }

            $pdo = $this->getDbConnection();
            $studentId = $this->getCurrentStudentId();
            $data = $this->request->getData();

            $videoId = $data['video_id'] ?? null;
            $courseId = $data['course_id'] ?? null;
            $watchTime = $data['watch_time'] ?? 0;
            $completed = $data['completed'] ?? false;

            if (!$videoId || !$courseId) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Dados inválidos']));
            }

            // Atualizar ou inserir progresso
            $stmt = $pdo->prepare("
                INSERT INTO student_progress (student_id, course_id, video_id, watch_time_seconds, completed_at, watched_at)
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    watch_time_seconds = VALUES(watch_time_seconds),
                    completed_at = CASE 
                        WHEN ? = 1 THEN NOW() 
                        ELSE completed_at 
                    END,
                    watched_at = NOW()
            ");

            $stmt->execute([
                $studentId,
                $courseId,
                $videoId,
                $watchTime,
                $completed ? date('Y-m-d H:i:s') : null,
                $completed ? 1 : 0
            ]);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true]));

        } catch (Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    /**
     * Página de compra do curso
     */
    public function purchaseStudents($courseId = null)
    {
        try {
            if (!$this->isStudent()) {
                $this->flash('Apenas estudantes podem comprar cursos.', 'error');
                return $this->redirect(['action' => 'index-students']);
            }

            $pdo = $this->getDbConnection();
            $studentId = $this->getCurrentStudentId();

            // Verificar se curso existe e está ativo
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $this->flash('Curso não encontrado.', 'error');
                return $this->redirect(['action' => 'index-students']);
            }

            // Verificar se já está inscrito
            $stmt = $pdo->prepare("
                SELECT * FROM course_enrollments 
                WHERE student_id = ? AND course_id = ?
            ");
            $stmt->execute([$studentId, $courseId]);
            $existingEnrollment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingEnrollment) {
                $this->flash('Você já possui acesso a este curso.', 'info');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Se for curso gratuito, redirecionar para inscrição
            if ($course['is_free']) {
                return $this->redirect(['action' => 'enroll', $courseId]);
            }

            // Para desenvolvimento, simular compra automática
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Simular processamento de pagamento
                $stmt = $pdo->prepare("
                    INSERT INTO course_enrollments (student_id, course_id, enrolled_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$studentId, $courseId]);

                $this->flash('Compra realizada com sucesso! Agora você tem acesso ao curso.', 'success');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            $this->set('course', $course);
            return $this->render('Admin/Courses/purchase_students');

        } catch (Exception $e) {
            $this->flash('Erro ao processar compra: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index-students']);
        }
    }
}