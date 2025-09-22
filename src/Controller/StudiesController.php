<?php
declare(strict_types=1);

namespace App\Controller;

use PDO;
use Exception;

class StudiesController extends AppController
{
    public function index()
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Se for estudante, mostrar apenas seus próprios estudos
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                if (!$studentId) {
                    $this->flash('Usuário estudante não está associado a nenhum registro de estudante.', 'error');
                    return $this->render('Studies/index');
                }
                $stmt = $pdo->prepare("
                    SELECT s.*, st.name as student_name, u.currency, u.username, u.role, u.active 
                    FROM studies s 
                    LEFT JOIN students st ON s.student_id = st.id 
                    LEFT JOIN users u ON st.id = u.student_id 
                    WHERE s.student_id = ? 
                    ORDER BY s.study_date DESC
                ");
                $stmt->execute([$studentId]);
            } else {
                // Admin vê todos os estudos
                $stmt = $pdo->query("
                    SELECT s.*, st.name as student_name, u.currency, u.username, u.role, u.active 
                    FROM studies s 
                    LEFT JOIN students st ON s.student_id = st.id 
                    LEFT JOIN users u ON st.id = u.student_id 
                    ORDER BY s.study_date DESC
                ");
            }
            
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Adicionar dados do usuário a cada estudo para compatibilidade com templates
            foreach ($studies as &$study) {
                $study['user'] = [
                    'currency' => $study['currency'] ?? 'BRL',
                    'username' => $study['username'],
                    'role' => $study['role'],
                    'active' => $study['active']
                ];
            }
            
            // Agrupar estudos por mês/ano
            $studiesByMonth = [];
            foreach ($studies as $study) {
                if (!empty($study['study_date'])) {
                    $date = new \DateTime($study['study_date']);
                    $monthYear = $date->format('Y-m');
                    $monthYearDisplay = $date->format('F Y');
                    
                    if (!isset($studiesByMonth[$monthYear])) {
                        $studiesByMonth[$monthYear] = [
                            'display' => $monthYearDisplay,
                            'studies' => [],
                            'total_studies' => 0,
                            'total_wins' => 0,
                            'total_losses' => 0,
                            'total_profit_loss' => 0
                        ];
                    }
                    
                    $studiesByMonth[$monthYear]['studies'][] = $study;
                    $studiesByMonth[$monthYear]['total_studies']++;
                    $studiesByMonth[$monthYear]['total_wins'] += $study['wins'] ?? 0;
                    $studiesByMonth[$monthYear]['total_losses'] += $study['losses'] ?? 0;
                    $studiesByMonth[$monthYear]['total_profit_loss'] += $study['profit_loss'] ?? 0;
                }
            }
            
            // Ordenar por mês/ano (mais recente primeiro)
            krsort($studiesByMonth);
            
            $this->set('studiesByMonth', $studiesByMonth);
            $this->set('studies', $studies); // Manter compatibilidade
            return $this->render('Studies/index');
        } catch (Exception $e) {
            $this->flash('Error loading studies: ' . $e->getMessage(), 'error');
            return $this->render('Studies/index');
        }
    }

    public function view($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("
                SELECT s.*, st.name as student_name, u.currency, u.username, u.role, u.active 
                FROM studies s 
                LEFT JOIN students st ON s.student_id = st.id 
                LEFT JOIN users u ON st.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $study = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($study) {
                // Adicionar dados do usuário ao array do study para compatibilidade com o template
                $study['user'] = [
                    'currency' => $study['currency'] ?? 'BRL',
                    'username' => $study['username'],
                    'role' => $study['role'],
                    'active' => $study['active']
                ];
            }
            
            if (!$study) {
                $this->flash('Study not found.', 'error');
                return $this->redirect('/studies');
            }
            
            // Verificar se estudante pode acessar este estudo
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                if ($study['student_id'] != $studentId) {
                    $this->flash('Acesso negado. Você só pode visualizar seus próprios estudos.', 'error');
                    return $this->redirect('/studies');
                }
            }
            
            $this->set('study', $study);
            return $this->render('Studies/view');
        } catch (Exception $e) {
            $this->flash('Error loading study: ' . $e->getMessage(), 'error');
            return $this->redirect('/studies');
        }
    }

    public function add()
    {
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            // Se for estudante, forçar o student_id para o estudante logado
            if ($this->isStudent()) {
                $data['student_id'] = $this->getCurrentStudentId();
            }
            
            try {
                $pdo = $this->getDbConnection();
                $stmt = $pdo->prepare("INSERT INTO studies (student_id, study_date, wins, losses, profit_loss, notes, created, modified) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([
                    $data['student_id'],
                    $data['study_date'],
                    $data['wins'],
                    $data['losses'],
                    $data['profit_loss'],
                    $data['notes'] ?? ''
                ]);
                
                $this->flash('The study has been saved.', 'success');
                return $this->redirect('/studies');
            } catch (Exception $e) {
                $this->flash('The study could not be saved. Please, try again.', 'error');
            }
        }
        
        // Get students for dropdown
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name FROM students ORDER BY name");
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->set('students', $students);
        } catch (Exception $e) {
            $this->set('students', []);
        }
        
        // Se for estudante, passar o ID do estudante atual para o template
        if ($this->isStudent()) {
            $this->set('currentStudentId', $this->getCurrentStudentId());
        }
        
        return $this->render('Studies/add');
    }

    public function edit($id = null)
    {
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            try {
                $pdo = $this->getDbConnection();
                $stmt = $pdo->prepare("UPDATE studies SET student_id = ?, study_date = ?, wins = ?, losses = ?, profit_loss = ?, notes = ?, modified = NOW() WHERE id = ?");
                $stmt->execute([
                    $data['student_id'],
                    $data['study_date'],
                    $data['wins'],
                    $data['losses'],
                    $data['profit_loss'],
                    $data['notes'] ?? '',
                    $id
                ]);
                
                $this->flash('The study has been updated.', 'success');
                return $this->redirect('/studies');
            } catch (Exception $e) {
                $this->flash('The study could not be updated. Please, try again.', 'error');
            }
        }
        
        // Get current study data
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("SELECT * FROM studies WHERE id = ?");
            $stmt->execute([$id]);
            $study = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$study) {
                $this->flash('Study not found.', 'error');
                return $this->redirect('/studies');
            }
            
            $this->set('study', $study);
        } catch (Exception $e) {
            $this->flash('Error loading study: ' . $e->getMessage(), 'error');
            return $this->redirect('/studies');
        }
        
        // Get students for dropdown
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name FROM students ORDER BY name");
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->set('students', $students);
        } catch (Exception $e) {
            $this->set('students', []);
        }
        
        return $this->render('Studies/edit');
    }

    public function delete($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM studies WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->flash('The study has been deleted.', 'success');
        } catch (Exception $e) {
            $this->flash('The study could not be deleted. Please, try again.', 'error');
        }
        
        return $this->redirect('/studies');
    }

    public function byStudent($studentId = null)
    {
        if (!$studentId) {
            $this->Flash->error(__('Invalid student ID.'));
            return $this->redirect(['controller' => 'Students', 'action' => 'index']);
        }

        $student = $this->Studies->Students->get($studentId);
        
        $this->paginate = [
            'conditions' => ['Studies.student_id' => $studentId],
            'contain' => ['Students'],
            'order' => ['Studies.study_date' => 'DESC']
        ];
        
        $studies = $this->paginate($this->Studies);

        $this->set(compact('studies', 'student'));
    }
}