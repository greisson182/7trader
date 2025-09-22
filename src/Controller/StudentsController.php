<?php
declare(strict_types=1);

namespace App\Controller;

use PDO;
use Exception;

class StudentsController extends AppController
{
    public function index()
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Se for estudante, mostrar apenas seus próprios dados
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                if (!$studentId) {
                    $this->flash('Usuário estudante não está associado a nenhum registro de estudante.', 'error');
                    return $this->render('Students/index');
                }
                $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
                $stmt->execute([$studentId]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Admin vê todos os estudantes
                $stmt = $pdo->query("SELECT * FROM students ORDER BY created DESC");
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            $this->set('students', $students);
            return $this->render('Students/index');
        } catch (Exception $e) {
            $this->flash('Error loading students: ' . $e->getMessage(), 'error');
            return $this->render('Students/index');
        }
    }

    public function view($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se estudante pode acessar este registro
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                if ($studentId != $id) {
                    $this->flash('Acesso negado. Você só pode visualizar seus próprios dados.', 'error');
                    return $this->redirect('/students');
                }
            }
            
            $stmt = $pdo->prepare("
                SELECT s.*, u.username, u.role, u.active 
                FROM students s 
                LEFT JOIN users u ON s.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Student not found.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Get related studies
            $stmt = $pdo->prepare("SELECT * FROM studies WHERE student_id = ? ORDER BY created DESC");
            $stmt->execute([$id]);
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set('student', $student);
            $this->set('studies', $studies);
            return $this->render('Students/view');
        } catch (Exception $e) {
            $this->flash('Error loading student: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        // Verificar se o usuário é administrador
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            try {
                $pdo = $this->getDbConnection();
                
                // Verificar se o email já existe na tabela students
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->fetch()) {
                    $this->flash('Este email já está cadastrado para outro estudante.', 'error');
                    return $this->render('Students/add');
                }
                
                // Verificar se o email já existe na tabela users
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->fetch()) {
                    $this->flash('Este email já está cadastrado como usuário.', 'error');
                    return $this->render('Students/add');
                }
                
                // Verificar se username foi fornecido e se já existe
                if (!empty($data['username'])) {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$data['username']]);
                    if ($stmt->fetch()) {
                        $this->flash('Este username já está em uso.', 'error');
                        return $this->render('Students/add');
                    }
                }
                
                // Iniciar transação
                $pdo->beginTransaction();
                
                // Inserir estudante
                $stmt = $pdo->prepare("INSERT INTO students (name, email, created, modified) VALUES (?, ?, NOW(), NOW())");
                $stmt->execute([$data['name'], $data['email']]);
                $studentId = $pdo->lastInsertId();
                
                // Processar username
                if (!empty($data['username'])) {
                    $username = $data['username'];
                } else {
                    // Criar username baseado no nome (remover espaços e caracteres especiais)
                    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['name']));
                    
                    // Verificar se o username já existe e adicionar número se necessário
                    $originalUsername = $username;
                    $counter = 1;
                    while (true) {
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                        $stmt->execute([$username]);
                        if (!$stmt->fetch()) {
                            break;
                        }
                        $username = $originalUsername . $counter;
                        $counter++;
                    }
                }
                
                // Processar senha
                if (!empty($data['password'])) {
                    $password = $data['password'];
                } else {
                    // Gerar senha temporária (primeiros 6 caracteres do nome + 123)
                    $password = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $data['name']), 0, 6)) . '123';
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Processar outros campos do usuário
                $role = $data['role'] ?? 'student';
                $active = isset($data['active']) ? 1 : 0;
                
                // Inserir usuário
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, student_id, active, created, modified) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$username, $data['email'], $hashedPassword, $role, $studentId, $active]);
                
                // Confirmar transação
                $pdo->commit();
                
                // Mensagem de sucesso personalizada
                $successMessage = "Estudante criado com sucesso! Username: {$username}";
                if (empty($data['password'])) {
                    $successMessage .= ", Senha temporária: {$password}";
                }
                $this->flash($successMessage, 'success');
                return $this->redirect(['action' => 'index']);
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                if ($pdo->inTransaction()) {
                    $pdo->rollback();
                }
                $this->flash('O estudante não pôde ser salvo. Erro: ' . $e->getMessage(), 'error');
            }
        }
        
        return $this->render('Students/add');
    }

    public function edit($id = null)
    {
        // Verificar se o usuário é administrador
        $this->requireAdmin();
        
        if (!$id) {
            $this->flash('ID do estudante não fornecido.', 'error');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            
            try {
                $pdo = $this->getDbConnection();
                
                // Verificar se o estudante existe
                $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
                $stmt->execute([$id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$student) {
                    $this->flash('Estudante não encontrado.', 'error');
                    return $this->redirect(['action' => 'index']);
                }
                
                // Verificar se o email já existe em outro estudante
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $id]);
                if ($stmt->fetch()) {
                    $this->flash('Este email já está cadastrado para outro estudante.', 'error');
                    // Buscar dados do usuário para reexibir o formulário
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
                    $stmt->execute([$id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $this->render('Students/edit', ['student' => $student, 'user' => $user]);
                }
                
                // Verificar se o username já existe em outro usuário (se fornecido)
                if (!empty($data['username'])) {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND student_id != ?");
                    $stmt->execute([$data['username'], $id]);
                    if ($stmt->fetch()) {
                        $this->flash('Este username já está em uso por outro usuário.', 'error');
                        // Buscar dados do usuário para reexibir o formulário
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
                        $stmt->execute([$id]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        return $this->render('Students/edit', ['student' => $student, 'user' => $user]);
                    }
                }
                
                // Iniciar transação
                $pdo->beginTransaction();
                
                // Atualizar estudante
                $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, modified = NOW() WHERE id = ?");
                $stmt->execute([$data['name'], $data['email'], $id]);
                
                // Buscar usuário associado
                $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Atualizar usuário existente
                    $updateFields = [];
                    $updateValues = [];
                    
                    // Username
                    if (!empty($data['username'])) {
                        $updateFields[] = "username = ?";
                        $updateValues[] = $data['username'];
                    }
                    
                    // Email
                    $updateFields[] = "email = ?";
                    $updateValues[] = $data['email'];
                    
                    // Role
                    if (!empty($data['role']) && in_array($data['role'], ['student', 'admin'])) {
                        $updateFields[] = "role = ?";
                        $updateValues[] = $data['role'];
                    }
                    
                    // Active status
                    $active = isset($data['active']) && $data['active'] == '1' ? 1 : 0;
                    $updateFields[] = "active = ?";
                    $updateValues[] = $active;
                    
                    // Password (se fornecida)
                    if (!empty($data['new_password'])) {
                        $updateFields[] = "password = ?";
                        $updateValues[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                    }
                    
                    // Modified timestamp
                    $updateFields[] = "modified = NOW()";
                    $updateValues[] = $id; // Para o WHERE
                    
                    $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE student_id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($updateValues);
                    
                } else {
                    // Criar novo usuário se não existir
                    $username = !empty($data['username']) ? $data['username'] : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['name']));
                    $role = !empty($data['role']) && in_array($data['role'], ['student', 'admin']) ? $data['role'] : 'student';
                    $active = isset($data['active']) && $data['active'] == '1' ? 1 : 0;
                    
                    // Verificar se o username já existe e adicionar número se necessário
                    $originalUsername = $username;
                    $counter = 1;
                    while (true) {
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                        $stmt->execute([$username]);
                        if (!$stmt->fetch()) {
                            break;
                        }
                        $username = $originalUsername . $counter;
                        $counter++;
                    }
                    
                    // Gerar senha (fornecida ou temporária)
                    $password = !empty($data['new_password']) ? $data['new_password'] : 'temp123';
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Inserir novo usuário
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, student_id, active, created, modified) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$username, $data['email'], $hashedPassword, $role, $id, $active]);
                    
                    $this->flash("Usuário criado com sucesso! Username: {$username}", 'info');
                }
                
                // Confirmar transação
                $pdo->commit();
                
                $this->flash('Estudante e dados de acesso atualizados com sucesso!', 'success');
                return $this->redirect(['action' => 'index']);
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                if ($pdo->inTransaction()) {
                    $pdo->rollback();
                }
                $this->flash('O estudante não pôde ser atualizado. Erro: ' . $e->getMessage(), 'error');
            }
        }
        
        // Buscar dados do estudante e usuário para exibir no formulário
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar estudante
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Estudante não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Buscar usuário associado
            $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $this->render('Students/edit', ['student' => $student, 'user' => $user]);
        } catch (Exception $e) {
            $this->flash('Erro ao carregar dados do estudante: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        // Verificar se o usuário é administrador
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->flash('Método não permitido.', 'error');
            return $this->redirect(['action' => 'index']);
        }
        
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se o estudante existe e buscar dados do usuário associado
            $stmt = $pdo->prepare("
                SELECT s.*, u.username, u.role, u.active 
                FROM students s 
                LEFT JOIN users u ON s.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Estudante não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Verificar se há estudos associados
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM studies WHERE student_id = ?");
            $stmt->execute([$id]);
            $studiesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($studiesCount > 0) {
                $this->flash("Não é possível excluir o estudante {$student['name']} pois há {$studiesCount} estudo(s) associado(s).", 'error');
                return $this->redirect(['action' => 'index']);
            }
            
            // Iniciar transação
            $pdo->beginTransaction();
            
            // Excluir usuário associado primeiro (se existir)
            $stmt = $pdo->prepare("DELETE FROM users WHERE student_id = ?");
            $stmt->execute([$id]);
            
            // Excluir estudante
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
            
            // Confirmar transação
            $pdo->commit();
            
            $this->flash("Estudante {$student['name']} e seu usuário foram excluídos com sucesso.", 'success');
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
            $this->flash('O estudante não pôde ser excluído. Erro: ' . $e->getMessage(), 'error');
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function metrics($id = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Buscar estudante com dados do usuário associado
            $stmt = $pdo->prepare("
                SELECT s.*, u.username, u.role, u.active 
                FROM students s 
                LEFT JOIN users u ON s.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Estudante não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }
        
            // Get current date parameters
            $currentDate = $this->request->getQuery('date', date('Y-m-d'));
            $currentYear = (int)$this->request->getQuery('year', date('Y'));
            $currentMonth = (int)$this->request->getQuery('month', date('n'));

            // Get daily metrics
            $dailyMetrics = $this->Students->getDailyMetrics($id, $currentDate);

            // Get monthly metrics
            $monthlyMetrics = $this->Students->getMonthlyMetrics($id, $currentYear, $currentMonth);

            // Get recent studies for context
            $recentStudies = $this->Students->Studies->find()
                ->where(['student_id' => $id])
                ->orderDesc('study_date')
                ->limit(10)
                ->toArray();

            // Calculate overall statistics
            $allStudies = $this->Students->Studies->find()
                ->where(['student_id' => $id])
                ->toArray();

            $overallStats = [
                'total_studies' => count($allStudies),
                'total_wins' => array_sum(array_column($allStudies, 'wins')),
                'total_losses' => array_sum(array_column($allStudies, 'losses')),
                'total_profit_loss' => array_sum(array_map('floatval', array_column($allStudies, 'profit_loss')))
            ];
        } catch (Exception $e) {
            $this->flash('Erro ao carregar métricas: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }

        $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
        $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0 
            ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2) 
            : 0;

        $this->set(compact(
            'student', 
            'dailyMetrics', 
            'monthlyMetrics', 
            'recentStudies', 
            'overallStats',
            'currentDate',
            'currentYear',
            'currentMonth'
        ));
    }

    public function dashboard($id = null)
    {
        try {
            // Se não foi passado ID, pegar o estudante atual (se for estudante logado)
            if (!$id) {
                $currentUser = $this->getCurrentUser();
                if ($currentUser && $currentUser['role'] === 'student') {
                    $id = $currentUser['student_id'];
                } else {
                    $this->flash('Por favor, selecione um estudante para visualizar o dashboard.', 'error');
                    return $this->redirect(['action' => 'index']);
                }
            }

            $pdo = $this->getDbConnection();
            
            // Buscar estudante com dados do usuário associado
            $stmt = $pdo->prepare("
                SELECT s.*, u.username, u.role, u.active 
                FROM students s 
                LEFT JOIN users u ON s.id = u.student_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Estudante não encontrado.', 'error');
                return $this->redirect(['action' => 'index']);
            }

            // Buscar mercados ativos para o filtro
            $stmt = $pdo->query("SELECT id, name, code FROM markets WHERE active = 1 ORDER BY name");
            $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obter ano selecionado do parâmetro GET
            $selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

            // Buscar dados dos estudos agrupados por mês (filtrado por ano)
            $stmt = $pdo->prepare("
                SELECT 
                    YEAR(study_date) as year,
                    MONTH(study_date) as month,
                    MONTHNAME(study_date) as month_name,
                    COUNT(*) as total_studies,
                    SUM(wins) as total_wins,
                    SUM(losses) as total_losses,
                    SUM(wins + losses) as total_trades,
                    ROUND(AVG(CASE WHEN (wins + losses) > 0 THEN (wins / (wins + losses)) * 100 ELSE 0 END), 2) as avg_win_rate,
                    SUM(profit_loss) as total_profit_loss,
                    MIN(study_date) as first_study,
                    MAX(study_date) as last_study,
                    GROUP_CONCAT(DISTINCT s.market_id) as market_ids
                FROM studies s
                WHERE student_id = ? AND YEAR(study_date) = ?
                GROUP BY YEAR(study_date), MONTH(study_date)
                ORDER BY year DESC, month DESC
            ");
            $stmt->execute([$id, $selectedYear]);
            $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular estatísticas gerais (filtrado por ano)
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_studies,
                    SUM(wins) as total_wins,
                    SUM(losses) as total_losses,
                    SUM(profit_loss) as total_profit_loss,
                    MIN(study_date) as first_study_date,
                    MAX(study_date) as last_study_date
                FROM studies 
                WHERE student_id = ? AND YEAR(study_date) = ?
            ");
            $stmt->execute([$id, $selectedYear]);
            $overallStats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calcular métricas adicionais
            $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
            $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0 
                ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2) 
                : 0;

            // Buscar dados para gráfico do ano selecionado (12 meses do ano)
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(s.study_date, '%Y-%m') as month_key,
                    YEAR(s.study_date) as year,
                    MONTH(s.study_date) as month,
                    SUM(s.profit_loss) as profit_loss,
                    SUM(s.wins) as wins,
                    SUM(s.losses) as losses,
                    s.market_id,
                    m.currency,
                    m.name as market_name,
                    m.code as market_code
                FROM studies s
                LEFT JOIN markets m ON s.market_id = m.id
                WHERE s.student_id = ? AND YEAR(s.study_date) = ?
                GROUP BY YEAR(s.study_date), MONTH(s.study_date), s.market_id
                ORDER BY year, month, market_name
            ");
            $stmt->execute([$id, $selectedYear]);
            $chartDataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Preparar dados para o gráfico - agregados por mês
            $chartDataByMonth = [];
            foreach ($chartDataRaw as $data) {
                $monthKey = $data['month_key'];
                if (!isset($chartDataByMonth[$monthKey])) {
                    $chartDataByMonth[$monthKey] = [
                        'year' => $data['year'],
                        'month' => $data['month'],
                        'profit_loss' => 0,
                        'wins' => 0,
                        'losses' => 0,
                        'markets' => []
                    ];
                }
                $chartDataByMonth[$monthKey]['profit_loss'] += $data['profit_loss'];
                $chartDataByMonth[$monthKey]['wins'] += $data['wins'];
                $chartDataByMonth[$monthKey]['losses'] += $data['losses'];
                $chartDataByMonth[$monthKey]['markets'][] = [
                    'market_id' => $data['market_id'],
                    'currency' => $data['currency'],
                    'market_name' => $data['market_name'],
                    'market_code' => $data['market_code'],
                    'profit_loss' => $data['profit_loss'],
                    'wins' => $data['wins'],
                    'losses' => $data['losses']
                ];
            }

            // Preparar dados para o gráfico
            $chartLabels = [];
            $chartProfitLoss = [];
            $chartWinRate = [];
            $chartDataDetailed = [];
            
            foreach ($chartDataByMonth as $monthKey => $data) {
                $chartLabels[] = date('M Y', mktime(0, 0, 0, $data['month'], 1, $data['year']));
                $chartProfitLoss[] = (float)$data['profit_loss'];
                $totalTrades = $data['wins'] + $data['losses'];
                $chartWinRate[] = $totalTrades > 0 ? round(($data['wins'] / $totalTrades) * 100, 2) : 0;
                $chartDataDetailed[] = [
                    'month_key' => $monthKey,
                    'year' => $data['year'],
                    'month' => $data['month'],
                    'markets' => $data['markets']
                ];
            }

            $this->set(compact(
                'student',
                'monthlyData',
                'overallStats',
                'chartLabels',
                'chartDataByMonth',
                'chartProfitLoss',
                'chartWinRate',
                'chartDataDetailed',
                'markets',
                'selectedYear'
            ));
            
            return $this->render('Students/dashboard');
            
        } catch (Exception $e) {
            $this->flash('Erro ao carregar dashboard: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function admin_dashboard()
    {
        // Verificar se é admin
        $this->requireAdmin();
        
        try {
            // Estatísticas gerais
            $totalStudents = $this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
            $activeStudents = $this->db->query("SELECT COUNT(*) FROM students s JOIN users u ON s.id = u.student_id WHERE u.active = 1")->fetchColumn();
            $totalStudies = $this->db->query("SELECT COUNT(*) FROM studies")->fetchColumn();
            
            // Estatísticas de performance
            $totalTrades = $this->db->query("SELECT SUM(wins + losses) FROM studies")->fetchColumn() ?: 0;
            $totalWins = $this->db->query("SELECT SUM(wins) FROM studies")->fetchColumn() ?: 0;
            $totalProfitLoss = $this->db->query("SELECT SUM(profit_loss) FROM studies")->fetchColumn() ?: 0;
            $overallWinRate = $totalTrades > 0 ? round(($totalWins / $totalTrades) * 100, 2) : 0;
            
            // Top 5 estudantes por profit/loss
            $topStudents = $this->db->query("
                SELECT s.name, s.id, SUM(st.profit_loss) as total_profit_loss, 
                       COUNT(st.id) as total_studies,
                       SUM(st.wins) as total_wins,
                       SUM(st.losses) as total_losses
                FROM students s 
                LEFT JOIN studies st ON s.id = st.student_id 
                LEFT JOIN users u ON s.id = u.student_id
                WHERE u.active = 1
                GROUP BY s.id, s.name 
                ORDER BY total_profit_loss DESC 
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Estudantes com pior performance
            $worstStudents = $this->db->query("
                SELECT s.name, s.id, SUM(st.profit_loss) as total_profit_loss, 
                       COUNT(st.id) as total_studies,
                       SUM(st.wins) as total_wins,
                       SUM(st.losses) as total_losses
                FROM students s 
                LEFT JOIN studies st ON s.id = st.student_id 
                LEFT JOIN users u ON s.id = u.student_id
                WHERE u.active = 1
                GROUP BY s.id, s.name 
                ORDER BY total_profit_loss ASC 
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Atividade recente (últimos 10 estudos)
            $recentActivity = $this->db->query("
                SELECT st.*, s.name as student_name
                FROM studies st 
                JOIN students s ON st.student_id = s.id 
                LEFT JOIN users u ON s.id = u.student_id
                ORDER BY st.study_date DESC, st.created DESC 
                LIMIT 10
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Dados para gráficos - estudos por mês
            $monthlyData = $this->db->query("
                SELECT 
                    YEAR(study_date) as year,
                    MONTH(study_date) as month,
                    COUNT(*) as total_studies,
                    SUM(wins) as total_wins,
                    SUM(losses) as total_losses,
                    SUM(profit_loss) as total_profit_loss
                FROM studies 
                WHERE study_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY YEAR(study_date), MONTH(study_date)
                ORDER BY year, month
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set(compact(
                'totalStudents',
                'activeStudents', 
                'totalStudies',
                'totalTrades',
                'totalWins',
                'totalProfitLoss',
                'overallWinRate',
                'topStudents',
                'worstStudents',
                'recentActivity',
                'monthlyData'
            ));
            
            return $this->render('Students/admin_dashboard');
            
        } catch (Exception $e) {
            $this->flash('Erro ao carregar dashboard administrativo: ' . $e->getMessage(), 'error');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function monthlyStudies($studentId = null, $year = null, $month = null)
    {
        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se estudante pode acessar este registro
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($currentStudentId != $studentId) {
                    $this->flash('Acesso negado. Você só pode visualizar seus próprios dados.', 'error');
                    return $this->redirect('/students');
                }
            }
            
            // Buscar dados do estudante
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $this->flash('Estudante não encontrado.', 'error');
                return $this->redirect('/students');
            }
            
            // Buscar estudos do mês específico
            $stmt = $pdo->prepare("
                SELECT * FROM studies 
                WHERE student_id = ? 
                AND YEAR(study_date) = ? 
                AND MONTH(study_date) = ?
                ORDER BY study_date DESC
            ");
            $stmt->execute([$studentId, $year, $month]);
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular estatísticas do mês
            $totalStudies = count($studies);
            $totalWins = array_sum(array_column($studies, 'wins'));
            $totalLosses = array_sum(array_column($studies, 'losses'));
            $totalTrades = $totalWins + $totalLosses;
            $totalProfitLoss = array_sum(array_column($studies, 'profit_loss'));
            $winRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;
            
            // Nome do mês em português
            $monthNames = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];
            $monthName = $monthNames[(int)$month] ?? 'Mês';
            
            $this->set(compact(
                'student',
                'studies',
                'year',
                'month',
                'monthName',
                'totalStudies',
                'totalWins',
                'totalLosses',
                'totalTrades',
                'totalProfitLoss',
                'winRate'
            ));
            
            return $this->render('Students/monthly_studies');
            
        } catch (Exception $e) {
            $this->flash('Erro ao carregar estudos mensais: ' . $e->getMessage(), 'error');
            return $this->redirect('/students');
        }
    }
}