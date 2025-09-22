<?php
session_start();

// Simular login do estudante
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'greisson';
$_SESSION['user_role'] = 'student';

echo "=== DEBUG DOS SEGMENTS ===\n";

// Simular diferentes URLs
$testUrls = [
    '/',
    '',
    '/admin',
    '/admin/',
    '/admin/students',
    '/admin/students/dashboard'
];

foreach ($testUrls as $testUrl) {
    echo "\n--- Testando URL: '$testUrl' ---\n";
    
    $path = $testUrl;
    if ($path === '/') {
        $path = '';
    }
    
    $segments = array_filter(explode('/', trim($path, '/')));
    
    echo "Path: '$path'\n";
    echo "Segments: " . json_encode(array_values($segments)) . "\n";
    echo "empty(\$segments[0]): " . (empty($segments[0]) ? 'true' : 'false') . "\n";
    echo "isset(\$_SESSION['user_id']): " . (isset($_SESSION['user_id']) ? 'true' : 'false') . "\n";
    
    $shouldRedirect = isset($_SESSION['user_id']) && empty($segments[0]);
    echo "Deveria redirecionar: " . ($shouldRedirect ? 'SIM' : 'NÃO') . "\n";
}

echo "\n=== TESTE REAL COM CURL ===\n";
$sessionId = session_id();
file_put_contents('debug_segments_session.txt', $sessionId);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if (preg_match('/Location: (.+)/', $response, $matches)) {
    echo "Location: " . trim($matches[1]) . "\n";
} else {
    echo "Nenhum redirecionamento encontrado\n";
}
?>