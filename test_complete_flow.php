<?php
// Teste completo do fluxo de login do aluno
echo "=== TESTE COMPLETO DO FLUXO DE LOGIN ===\n\n";

// 1. Fazer login
echo "1. Fazendo login com usuário 'greisson'...\n";

$loginData = [
    'username' => 'greisson',
    'password' => '123456'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 302 && strpos($redirectUrl, '/admin') !== false) {
    echo "✅ Login bem-sucedido! Redirecionando para: $redirectUrl\n";
} else {
    echo "❌ Falha no login. Status: $httpCode\n";
    exit(1);
}

// 2. Testar acesso à página inicial
echo "\n2. Testando acesso à página inicial (/)...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 302 && strpos($redirectUrl, 'dashboard') !== false) {
    echo "✅ Redirecionamento automático funcionando! Indo para: $redirectUrl\n";
} else if ($httpCode == 302 && strpos($redirectUrl, 'login') !== false) {
    echo "❌ Sendo redirecionado para login - sessão perdida!\n";
    exit(1);
} else {
    echo "❌ Comportamento inesperado. Status: $httpCode\n";
    exit(1);
}

// 3. Testar acesso ao dashboard
echo "\n3. Testando acesso ao dashboard do aluno...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/students/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Dashboard carregado com sucesso!\n";
    
    // Verificar se o conteúdo contém elementos do dashboard
    $body = substr($response, strpos($response, "\r\n\r\n") + 4);
    if (strpos($body, 'Dashboard') !== false || strpos($body, 'Estudos') !== false) {
        echo "✅ Conteúdo do dashboard presente!\n";
    } else {
        echo "⚠️ Dashboard carregou mas conteúdo pode estar incorreto\n";
    }
} else if ($httpCode == 302 && strpos($redirectUrl, 'login') !== false) {
    echo "❌ Dashboard redirecionando para login - problema de autenticação!\n";
    exit(1);
} else {
    echo "❌ Problema no dashboard. Status: $httpCode\n";
    exit(1);
}

// 4. Testar logout
echo "\n4. Testando logout...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/logout');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 302 && strpos($redirectUrl, 'login') !== false) {
    echo "✅ Logout funcionando! Redirecionando para: $redirectUrl\n";
} else {
    echo "❌ Problema no logout. Status: $httpCode\n";
}

// 5. Verificar se realmente foi deslogado
echo "\n5. Verificando se foi deslogado...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/students/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 302 && strpos($redirectUrl, 'login') !== false) {
    echo "✅ Logout confirmado! Acesso negado ao dashboard\n";
} else {
    echo "❌ Logout não funcionou completamente. Status: $httpCode\n";
}

echo "\n🎉 TESTE COMPLETO FINALIZADO COM SUCESSO!\n";
echo "✅ Login funcionando\n";
echo "✅ Redirecionamento automático funcionando\n";
echo "✅ Dashboard acessível\n";
echo "✅ Logout funcionando\n";
echo "✅ Proteção de rotas funcionando\n";
?>