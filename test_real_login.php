<?php
echo "=== TESTE DE LOGIN REAL ===\n";

// Dados de login
$loginData = [
    'username' => 'greisson',
    'password' => 'admin123'
];

echo "Tentando fazer login com: {$loginData['username']}\n\n";

// Fazer POST para /login
$postData = http_build_query($loginData);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                   "Content-Length: " . strlen($postData) . "\r\n",
        'content' => $postData
    ]
]);

echo "Fazendo POST para http://localhost:8000/login\n";
$response = file_get_contents('http://localhost:8000/login', false, $context);

if ($response === false) {
    echo "❌ Erro ao fazer login\n";
    exit;
}

echo "✅ Resposta recebida\n";

// Verificar se há redirecionamento ou mensagem de sucesso
if (strpos($response, 'Login realizado com sucesso') !== false) {
    echo "✅ Login bem-sucedido!\n";
} elseif (strpos($response, 'Usuário ou senha inválidos') !== false) {
    echo "❌ Credenciais inválidas\n";
} else {
    echo "ℹ️ Resposta não reconhecida\n";
    echo "Primeiros 300 caracteres da resposta:\n";
    echo substr($response, 0, 300) . "...\n";
}

// Verificar headers de resposta
$headers = $http_response_header ?? [];
echo "\nHeaders de resposta:\n";
foreach ($headers as $header) {
    echo "  $header\n";
    if (strpos($header, 'Location:') === 0) {
        echo "  🔄 REDIRECIONAMENTO DETECTADO!\n";
    }
    if (strpos($header, 'Set-Cookie:') === 0) {
        echo "  🍪 COOKIE DEFINIDO!\n";
    }
}
?>