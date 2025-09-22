<?php
session_start();

// Simular login do estudante
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'greisson';
$_SESSION['user_role'] = 'student';

echo "=== DEBUG isLoggedIn ===\n";
echo "Sessão atual:\n";
foreach ($_SESSION as $key => $value) {
    echo "  $key: $value\n";
}

// Simular o método isLoggedIn do AppController
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

echo "\n=== TESTE isLoggedIn ===\n";
echo "isset(\$_SESSION['user_id']): " . (isset($_SESSION['user_id']) ? 'true' : 'false') . "\n";
echo "!empty(\$_SESSION['user_id']): " . (!empty($_SESSION['user_id']) ? 'true' : 'false') . "\n";
echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "\n";

// Salvar session ID para teste
$sessionId = session_id();
file_put_contents('isloggedin_session_id.txt', $sessionId);
echo "\nSession ID salvo: $sessionId\n";

// Testar com cURL
echo "\n=== TESTE COM CURL ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if (preg_match('/Location: (.+)/', $response, $matches)) {
    echo "Redirecionando para: " . trim($matches[1]) . "\n";
}
?>