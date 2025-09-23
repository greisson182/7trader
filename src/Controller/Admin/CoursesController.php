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
        if ($this->request->getMethod() === 'POST') {
            try {
                $pdo = $this->getDbConnection();
                $data = $this->request->getData();
                
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
            
            if ($this->request->getMethod() === 'POST') {
                $data = $this->request->getData();
                
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
            
            if ($this->request->getMethod() === 'POST') {
                $data = $this->request->getData();
                
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
}