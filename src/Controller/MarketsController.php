<?php
declare(strict_types=1);

namespace App\Controller;

use PDO;
use Exception;

class MarketsController extends AppController
{
    public function index()
    {
        try {
            $pdo = $this->getDbConnection();
            
            $stmt = $pdo->query("SELECT * FROM markets ORDER BY name ASC");
            $markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set('markets', $markets);
            return $this->render('Markets/index');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar mercados: ' . $e->getMessage(), 'error');
            return $this->render('Markets/index');
        }
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->flash('ID do mercado não fornecido.', 'error');
            return $this->redirect('/markets');
        }

        try {
            $pdo = $this->getDbConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE id = ?");
            $stmt->execute([$id]);
            $market = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$market) {
                $this->flash('Mercado não encontrado.', 'error');
                return $this->redirect('/markets');
            }

            // Buscar estudos relacionados a este mercado
            $stmt = $pdo->prepare("
                SELECT s.*, st.name as student_name 
                FROM studies s 
                LEFT JOIN students st ON s.student_id = st.id 
                WHERE s.market_id = ? 
                ORDER BY s.study_date DESC 
                LIMIT 10
            ");
            $stmt->execute([$id]);
            $relatedStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->set('market', $market);
            $this->set('relatedStudies', $relatedStudies);
            return $this->render('Markets/view');
        } catch (Exception $e) {
            $this->flash('Erro ao carregar mercado: ' . $e->getMessage(), 'error');
            return $this->redirect('/markets');
        }
    }

    public function add()
    {
        if ($this->isPost()) {
            $data = $this->getData();
            
            // Validação básica
            if (empty($data['name']) || empty($data['code']) || empty($data['currency'])) {
                $this->flash('Nome, código e moeda são obrigatórios.', 'error');
                $this->set('market', $data);
                return $this->render('Markets/add');
            }

            // Validar moeda
            $validCurrencies = ['BRL', 'USD', 'EUR'];
            if (!in_array($data['currency'], $validCurrencies)) {
                $this->flash('Moeda inválida. Use: BRL, USD ou EUR.', 'error');
                $this->set('market', $data);
                return $this->render('Markets/add');
            }

            try {
                $pdo = $this->getDbConnection();
                
                // Verificar se o código já existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM markets WHERE code = ?");
                $stmt->execute([$data['code']]);
                if ($stmt->fetchColumn() > 0) {
                    $this->flash('Código do mercado já existe.', 'error');
                    $this->set('market', $data);
                    return $this->render('Markets/add');
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO markets (name, code, description, currency, active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $active = isset($data['active']) ? 1 : 0;
                $stmt->execute([
                    $data['name'],
                    $data['code'],
                    $data['description'] ?? null,
                    $data['currency'],
                    $active
                ]);
                
                $this->flash('Mercado criado com sucesso!', 'success');
                return $this->redirect('/markets');
            } catch (Exception $e) {
                $this->flash('Erro ao criar mercado: ' . $e->getMessage(), 'error');
                $this->set('market', $data);
                return $this->render('Markets/add');
            }
        }
        
        $this->set('market', []);
        return $this->render('Markets/add');
    }

    public function edit($id = null)
    {
        if (!$id) {
            $this->flash('ID do mercado não fornecido.', 'error');
            return $this->redirect('/markets');
        }

        try {
            $pdo = $this->getDbConnection();
            
            if ($this->isPost()) {
                $data = $this->getData();
                
                // Validação básica
                if (empty($data['name']) || empty($data['code']) || empty($data['currency'])) {
                    $this->flash('Nome, código e moeda são obrigatórios.', 'error');
                    $this->set('market', $data);
                    return $this->render('Markets/edit');
                }

                // Validar moeda
                $validCurrencies = ['BRL', 'USD', 'EUR'];
                if (!in_array($data['currency'], $validCurrencies)) {
                    $this->flash('Moeda inválida. Use: BRL, USD ou EUR.', 'error');
                    $this->set('market', $data);
                    return $this->render('Markets/edit');
                }

                // Verificar se o código já existe (exceto para o próprio registro)
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM markets WHERE code = ? AND id != ?");
                $stmt->execute([$data['code'], $id]);
                if ($stmt->fetchColumn() > 0) {
                    $this->flash('Código do mercado já existe.', 'error');
                    $this->set('market', $data);
                    return $this->render('Markets/edit');
                }
                
                $stmt = $pdo->prepare("
                    UPDATE markets 
                    SET name = ?, code = ?, description = ?, currency = ?, active = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                
                $active = isset($data['active']) ? 1 : 0;
                $stmt->execute([
                    $data['name'],
                    $data['code'],
                    $data['description'] ?? null,
                    $data['currency'],
                    $active,
                    $id
                ]);
                
                $this->flash('Mercado atualizado com sucesso!', 'success');
                return $this->redirect('/markets');
            }
            
            // Buscar dados do mercado
            $stmt = $pdo->prepare("SELECT * FROM markets WHERE id = ?");
            $stmt->execute([$id]);
            $market = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$market) {
                $this->flash('Mercado não encontrado.', 'error');
                return $this->redirect('/markets');
            }
            
            $this->set('market', $market);
            return $this->render('Markets/edit');
        } catch (Exception $e) {
            $this->flash('Erro ao editar mercado: ' . $e->getMessage(), 'error');
            return $this->redirect('/markets');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            $this->flash('ID do mercado não fornecido.', 'error');
            return $this->redirect('/markets');
        }

        try {
            $pdo = $this->getDbConnection();
            
            // Verificar se existem estudos associados
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM studies WHERE market_id = ?");
            $stmt->execute([$id]);
            $studiesCount = $stmt->fetchColumn();
            
            if ($studiesCount > 0) {
                $this->flash('Não é possível excluir o mercado pois existem ' . $studiesCount . ' estudos associados.', 'error');
                return $this->redirect('/markets');
            }
            
            $stmt = $pdo->prepare("DELETE FROM markets WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                $this->flash('Mercado excluído com sucesso!', 'success');
            } else {
                $this->flash('Mercado não encontrado.', 'error');
            }
            
            return $this->redirect('/markets');
        } catch (Exception $e) {
            $this->flash('Erro ao excluir mercado: ' . $e->getMessage(), 'error');
            return $this->redirect('/markets');
        }
    }
}