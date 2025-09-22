<?php
declare(strict_types=1);

namespace App\Controller;

use PDO;
use Exception;

class ProfileController extends AppController
{
    public function edit()
    {
        // Verificar se o usuário está logado
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->flash('Você precisa estar logado para editar seu perfil.', 'error');
            return $this->redirect('/login');
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            
            try {
                $pdo = $this->getDbConnection();
                
                // Verificar se o email já existe em outro usuário
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $currentUser['id']]);
                if ($stmt->fetch()) {
                    $this->flash('Este email já está cadastrado para outro usuário.', 'error');
                    return $this->render('Profile/edit', ['user' => $currentUser]);
                }
                
                // Verificar se o username já existe em outro usuário (se fornecido)
                if (!empty($data['username']) && $data['username'] !== $currentUser['username']) {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                    $stmt->execute([$data['username'], $currentUser['id']]);
                    if ($stmt->fetch()) {
                        $this->flash('Este username já está em uso por outro usuário.', 'error');
                        return $this->render('Profile/edit', ['user' => $currentUser]);
                    }
                }
                
                // Preparar campos para atualização
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
                
                // Password (se fornecida)
                if (!empty($data['new_password'])) {
                    // Verificar senha atual se fornecida
                    if (!empty($data['current_password'])) {
                        if (!password_verify($data['current_password'], $currentUser['password'])) {
                            $this->flash('Senha atual incorreta.', 'error');
                            return $this->render('Profile/edit', ['user' => $currentUser]);
                        }
                    }
                    
                    $updateFields[] = "password = ?";
                    $updateValues[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                }
                
                // Modified timestamp
                $updateFields[] = "modified = NOW()";
                $updateValues[] = $currentUser['id']; // Para o WHERE
                
                $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($updateValues);
                
                // Atualizar dados do estudante se o usuário for um estudante
                if ($currentUser['role'] === 'student' && $currentUser['student_id']) {
                    $stmt = $pdo->prepare("UPDATE students SET email = ?, modified = NOW() WHERE id = ?");
                    $stmt->execute([$data['email'], $currentUser['student_id']]);
                }
                
                $this->flash('Perfil atualizado com sucesso!', 'success');
                
                // Atualizar sessão com novos dados
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$currentUser['id']]);
                $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user'] = $updatedUser;
                
                return $this->redirect('/profile/edit');
                
            } catch (Exception $e) {
                $this->flash('O perfil não pôde ser atualizado. Erro: ' . $e->getMessage(), 'error');
            }
        }
        
        // Buscar dados atualizados do usuário
        try {
            $pdo = $this->getDbConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$currentUser['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                $this->flash('Usuário não encontrado.', 'error');
                return $this->redirect('/');
            }
            
            return $this->render('Profile/edit', ['user' => $user]);
            
        } catch (Exception $e) {
            $this->flash('Erro ao carregar dados do perfil: ' . $e->getMessage(), 'error');
            return $this->redirect('/');
        }
    }
}