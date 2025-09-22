<?php
echo "=== TESTE FINAL DE LOGIN DO ESTUDANTE ===\n";

// Simular dados de login
$loginData = [
    'username' => 'greisson',
    'password' => 'admin123'
];

echo "1. Fazendo login com: {$loginData['username']}\n";

// Fazer POST para /login usando cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não seguir redirecionamentos
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Salvar cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Usar cookies

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "2. Status do login: HTTP $httpCode\n";

// Verificar se houve redirecionamento
if ($httpCode === 302) {
    echo "✅ Login bem-sucedido (redirecionamento detectado)\n";
    
    // Extrair cookie da resposta
    preg_match('/Set-Cookie: PHPSESSID=([^;]+)/', $response, $matches);
    if (isset($matches[1])) {
        $sessionId = $matches[1];
        echo "3. Session ID: $sessionId\n";
        
        // Testar acesso ao /admin com o cookie
        echo "4. Testando acesso ao /admin...\n";
        
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, 'http://localhost:8000/admin');
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch2, CURLOPT_HEADER, true);
        curl_setopt($ch2, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
        
        $adminResponse = curl_exec($ch2);
        $adminHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);
        
        echo "5. Status do acesso ao admin: HTTP $adminHttpCode\n";
        
        if ($adminHttpCode === 200) {
            echo "✅ SUCESSO! Estudante pode acessar o /admin\n";
        } elseif ($adminHttpCode === 302) {
            // Verificar para onde está redirecionando
            preg_match('/Location: (.+)/', $adminResponse, $locationMatches);
            if (isset($locationMatches[1])) {
                $redirectUrl = trim($locationMatches[1]);
                echo "🔄 Redirecionamento para: $redirectUrl\n";
                
                if (strpos($redirectUrl, '/admin/students/dashboard/') !== false) {
                    echo "✅ SUCESSO! Estudante está sendo redirecionado para seu dashboard\n";
                } else {
                    echo "❌ Redirecionamento inesperado\n";
                }
            }
        } else {
            echo "❌ Falha no acesso ao admin\n";
        }
    } else {
        echo "❌ Cookie de sessão não encontrado\n";
    }
} else {
    echo "❌ Falha no login\n";
    echo "Resposta:\n" . substr($response, 0, 500) . "...\n";
}

// Limpar arquivo de cookies
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}
?>