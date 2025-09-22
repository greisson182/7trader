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
                    SELECT s.*, st.name as student_name, m.name as market_name, m.currency, u.username, u.role, u.active 
                    FROM studies s 
                    LEFT JOIN students st ON s.student_id = st.id 
                    LEFT JOIN markets m ON s.market_id = m.id
                    LEFT JOIN users u ON st.id = u.student_id 
                    WHERE s.student_id = ? 
                    ORDER BY s.study_date DESC
                ");
                $stmt->execute([$studentId]);
            } else {
                // Admin vê todos os estudos
                $stmt = $pdo->query("
                    SELECT s.*, st.name as student_name, m.name as market_name, m.currency, u.username, u.role, u.active 
                    FROM studies s 
                    LEFT JOIN students st ON s.student_id = st.id 
                    LEFT JOIN markets m ON s.market_id = m.id
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
                SELECT s.*, st.name as student_name, st.email as student_email, 
                       m.name as market_name, m.code as market_code, m.description as market_description, m.currency,
                       u.username, u.role, u.active 
                FROM studies s 
                LEFT JOIN students st ON s.student_id = st.id 
                LEFT JOIN markets m ON s.market_id = m.id
                LEFT JOIN users u ON st.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $study = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($study) {
                // Adicionar dados do usuário ao array do study para compatibilidade com o template
                $study['user'] = [
                    'currency' => $study['currency'] ?? 'BRL', // Agora vem do market
                    'username' => $study['username'],
                    'role' => $study['role'],
                    'active' => $study['active']
                ];
                
                // Adicionar dados do estudante ao array do study para compatibilidade com o template
                $study['student'] = [
                    'id' => $study['student_id'],
                    'name' => $study['student_name'],
                    'email' => $study['student_email'] ?? $study['username'] // Usar email do estudante ou username como fallback
                ];
                
                // Adicionar dados do mercado ao array do study
                if ($study['market_name']) {
                    $study['market'] = [
                        'name' => $study['market_name'],
                        'code' => $study['market_code'],
                        'description' => $study['market_description']
                    ];
                } else {
                    $study['market'] = null;
                }
                
                // Calcular campos derivados
                $study['total_trades'] = $study['wins'] + $study['losses'];
                $study['win_rate'] = $study['total_trades'] > 0 ? ($study['wins'] / $study['total_trades']) * 100 : 0;
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
                $stmt = $pdo->prepare("INSERT INTO studies (student_id, market_id, study_date, wins, losses, profit_loss, notes, created, modified) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([
                    $data['student_id'],
                    $data['market_id'],
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
        
        // Get students for dropdown - Not needed anymore since we removed the dropdown
        // Keeping this commented for reference
        /*
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name FROM students ORDER BY name");
            $studentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert to associative array with id as key and name as value
            $students = [];
            foreach ($studentsData as $student) {
                $students[$student['id']] = $student['name'];
            }
            
            $this->set('students', $students);
        } catch (Exception $e) {
            $this->set('students', []);
        }
        */
        
        // Se for estudante, passar o ID do estudante atual para o template
        if ($this->isStudent()) {
            $this->set('currentStudentId', $this->getCurrentStudentId());
        }
        
        // Carregar mercados para o dropdown
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name, code FROM markets WHERE active = 1 ORDER BY name");
            $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->set('markets', $markets);
        } catch (Exception $e) {
            $this->set('markets', []);
        }
        
        return $this->render('Studies/add');
    }

    public function edit($id = null)
    {
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            // Security check for POST request: verify ownership before updating
            try {
                $pdo = $this->getDbConnection();
                $stmt = $pdo->prepare("SELECT student_id FROM studies WHERE id = ?");
                $stmt->execute([$id]);
                $study = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$study) {
                    $this->flash('Estudo não encontrado.', 'error');
                    return $this->redirect('/studies');
                }
                
                // Security validation
                $currentUser = $this->getCurrentUser();
                if (!$currentUser) {
                    $this->flash('Acesso negado. Faça login para continuar.', 'error');
                    return $this->redirect('/login');
                }
                
                if ($this->isStudent()) {
                    $currentStudentId = $this->getCurrentStudentId();
                    if ($study['student_id'] != $currentStudentId) {
                        $this->flash('Acesso negado. Você só pode editar seus próprios estudos.', 'error');
                        return $this->redirect('/studies');
                    }
                    // Force the student_id to be the current student's ID to prevent tampering
                    $data['student_id'] = $currentStudentId;
                }
                
                $stmt = $pdo->prepare("UPDATE studies SET student_id = ?, market_id = ?, study_date = ?, wins = ?, losses = ?, profit_loss = ?, notes = ?, modified = NOW() WHERE id = ?");
                $stmt->execute([
                    $data['student_id'],
                    $data['market_id'],
                    $data['study_date'],
                    $data['wins'],
                    $data['losses'],
                    $data['profit_loss'],
                    $data['notes'] ?? '',
                    $id
                ]);
                
                $this->flash('O estudo foi atualizado com sucesso.', 'success');
                return $this->redirect('/studies');
            } catch (Exception $e) {
                $this->flash('O estudo não pôde ser atualizado. Tente novamente.', 'error');
            }
        }
        
        // Get current study data
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("SELECT s.*, st.name as student_name FROM studies s JOIN students st ON s.student_id = st.id WHERE s.id = ?");
            $stmt->execute([$id]);
            $study = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$study) {
                $this->flash('Study not found.', 'error');
                return $this->redirect('/studies');
            }
            
            // Security check: Only allow editing if user is logged in and owns the study
            $currentUser = $this->getCurrentUser();
            if (!$currentUser) {
                $this->flash('Acesso negado. Faça login para continuar.', 'error');
                return $this->redirect('/login');
            }
            
            // If user is a student, they can only edit their own studies
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($study['student_id'] != $currentStudentId) {
                    $this->flash('Acesso negado. Você só pode editar seus próprios estudos.', 'error');
                    return $this->redirect('/studies');
                }
            }
            // Admins can edit any study (no additional check needed)
            
            // Calculate additional fields for display
            $totalTrades = ($study['wins'] ?? 0) + ($study['losses'] ?? 0);
            $winRate = $totalTrades > 0 ? round(($study['wins'] / $totalTrades) * 100, 2) : 0;
            
            $study['total_trades'] = $totalTrades;
            $study['win_rate'] = $winRate;
            
            $this->set('study', $study);
            $this->set('studentName', $study['student_name']);
        } catch (Exception $e) {
            $this->flash('Error loading study: ' . $e->getMessage(), 'error');
            return $this->redirect('/studies');
        }
        
        // Get students for dropdown
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name FROM students ORDER BY name");
            $studentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert to associative array with id as key and name as value
            $students = [];
            foreach ($studentsData as $student) {
                $students[$student['id']] = $student['name'];
            }
            
            $this->set('students', $students);
        } catch (Exception $e) {
            $this->set('students', []);
        }
        
        // Carregar mercados para o dropdown
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->query("SELECT id, name, code FROM markets WHERE active = 1 ORDER BY name");
            $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->set('markets', $markets);
        } catch (Exception $e) {
            $this->set('markets', []);
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