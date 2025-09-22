<?php
session_start();

// Simular login do estudante
$_SESSION['user_id'] = 2;  // ID do estudante greisson
$_SESSION['username'] = 'greisson';
$_SESSION['role'] = 'student';

echo "=== TESTE DE ACESSO AO ADMIN ===\n";
echo "Sessão criada:\n";
foreach ($_SESSION as $key => $value) {
    echo "  $key: $value\n";
}

echo "\nSession ID: " . session_id() . "\n";

// Testar acesso via cURL
echo "\n=== TESTANDO ACESSO VIA HTTP ===\n";

// Criar cookie com session ID
$sessionCookie = "PHPSESSID=" . session_id();

// Testar acesso ao /admin
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, $sessionCookie);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

curl_close($ch);

echo "Status Code: $httpCode\n";
if ($redirectUrl) {
    echo "Redirect URL: $redirectUrl\n";
}

// Separar headers e body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "\nHeaders:\n";
echo $headers;

echo "\nBody (primeiros 500 caracteres):\n";
echo substr($body, 0, 500) . "...\n";

// Verificar se há redirecionamento
if (strpos($headers, 'Location:') !== false) {
    preg_match('/Location: (.+)/', $headers, $matches);
    if (isset($matches[1])) {
        echo "\n🔄 REDIRECIONAMENTO DETECTADO: " . trim($matches[1]) . "\n";
    }
}

echo "\n=== RESULTADO ===\n";
if ($httpCode === 200) {
    echo "✅ Acesso permitido ao /admin\n";
} elseif ($httpCode === 302 || $httpCode === 301) {
    echo "🔄 Redirecionamento detectado\n";
} else {
    echo "❌ Acesso negado (HTTP $httpCode)\n";
}
?>