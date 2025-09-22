<?php
// Debug completo do fluxo de login
session_start();

echo "=== DEBUG DO FLUXO DE LOGIN ===\n\n";

// Limpar sessão anterior
session_destroy();
session_start();

echo "1. Testando LOGIN do aluno...\n";

// Dados de login
$loginData = [
    'email' => 'aluno1@teste.com',
    'password' => '123456'
];

// Fazer login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Status do login: $httpCode\n";
echo "URL de redirecionamento: " . ($redirectUrl ?: 'Nenhuma') . "\n";

// Extrair headers
$headerSize = strpos($response, "\r\n\r\n");
$headers = substr($response, 0, $headerSize);
echo "Headers do login:\n$headers\n\n";

// Verificar se há Set-Cookie
if (preg_match('/Set-Cookie: ([^;]+)/', $headers, $matches)) {
    echo "Cookie definido: " . $matches[1] . "\n\n";
} else {
    echo "❌ Nenhum cookie foi definido no login!\n\n";
}

echo "2. Testando acesso à página inicial (/)...\n";

// Testar acesso à página inicial
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Status da página inicial: $httpCode\n";
echo "URL de redirecionamento: " . ($redirectUrl ?: 'Nenhuma') . "\n";

// Extrair headers
$headerSize = strpos($response, "\r\n\r\n");
$headers = substr($response, 0, $headerSize);
echo "Headers da página inicial:\n$headers\n\n";

echo "3. Testando acesso direto ao dashboard...\n";

// Testar acesso direto ao dashboard
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/students/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Status do dashboard: $httpCode\n";
echo "URL de redirecionamento: " . ($redirectUrl ?: 'Nenhuma') . "\n";

// Extrair headers
$headerSize = strpos($response, "\r\n\r\n");
$headers = substr($response, 0, $headerSize);
echo "Headers do dashboard:\n$headers\n\n";

// Verificar conteúdo da resposta
$body = substr($response, $headerSize + 4);
if (strpos($body, 'login') !== false) {
    echo "❌ O dashboard está redirecionando para login!\n";
} else {
    echo "✅ Dashboard carregou corretamente\n";
}

echo "\n4. Verificando cookies salvos...\n";
if (file_exists('cookies.txt')) {
    $cookies = file_get_contents('cookies.txt');
    echo "Cookies salvos:\n$cookies\n";
} else {
    echo "❌ Arquivo de cookies não encontrado!\n";
}
?>