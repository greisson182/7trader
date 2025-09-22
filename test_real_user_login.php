<?php
// Teste com usuário real
session_start();

echo "=== TESTE COM USUÁRIO REAL ===\n\n";

// Limpar sessão anterior
session_destroy();
session_start();

echo "1. Testando LOGIN com usuário real (greisson)...\n";

// Dados de login com usuário real
$loginData = [
    'username' => 'greisson',
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
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_real.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_real.txt');

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

// Verificar conteúdo da resposta
$body = substr($response, $headerSize + 4);
if (strpos($body, 'Login realizado com sucesso') !== false) {
    echo "✅ Login bem-sucedido!\n";
} else if (strpos($body, 'inválidos') !== false) {
    echo "❌ Credenciais inválidas!\n";
} else {
    echo "Resposta do login:\n" . substr($body, 0, 500) . "...\n";
}

echo "\n2. Testando acesso à página inicial (/)...\n";

// Testar acesso à página inicial
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_real.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Status da página inicial: $httpCode\n";
echo "URL de redirecionamento: " . ($redirectUrl ?: 'Nenhuma') . "\n";

if ($httpCode == 302 && strpos($redirectUrl, 'dashboard') !== false) {
    echo "✅ Redirecionamento para dashboard funcionando!\n";
} else if ($httpCode == 302 && strpos($redirectUrl, 'login') !== false) {
    echo "❌ Redirecionando para login - sessão não mantida!\n";
} else {
    echo "Status inesperado\n";
}

echo "\n3. Verificando cookies salvos...\n";
if (file_exists('cookies_real.txt')) {
    $cookies = file_get_contents('cookies_real.txt');
    echo "Cookies salvos:\n$cookies\n";
} else {
    echo "❌ Arquivo de cookies não encontrado!\n";
}
?>