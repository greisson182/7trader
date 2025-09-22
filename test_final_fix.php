<?php
echo "=== TESTE FINAL DO FIX ===\n";

// Teste 1: Login do estudante
echo "\n1. Testando login do estudante...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => 'greisson',
    'password' => 'admin123'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status do login: $httpCode\n";
if ($httpCode == 302) {
    echo "✅ Login realizado com sucesso\n";
    
    // Extrair session ID do cookie
    if (preg_match('/Set-Cookie: PHPSESSID=([^;]+)/', $response, $matches)) {
        $sessionId = $matches[1];
        echo "Session ID: $sessionId\n";
        
        // Teste 2: Acessar /admin
        echo "\n2. Testando acesso ao /admin...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Status do /admin: $httpCode\n";
        if ($httpCode == 200) {
            echo "✅ Acesso ao /admin funcionando!\n";
            echo "✅ PROBLEMA RESOLVIDO!\n";
        } elseif ($httpCode == 302) {
            if (preg_match('/Location: (.+)/', $response, $matches)) {
                $location = trim($matches[1]);
                echo "Redirecionando para: $location\n";
                if ($location === '/login') {
                    echo "❌ Ainda redirecionando para login\n";
                } else {
                    echo "ℹ️ Redirecionando para: $location\n";
                }
            }
        }
        
        // Teste 3: Acessar dashboard específico
        echo "\n3. Testando acesso ao dashboard específico...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/students/dashboard/1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Status do dashboard específico: $httpCode\n";
        if ($httpCode == 200) {
            echo "✅ Dashboard específico funcionando!\n";
        }
        
    } else {
        echo "❌ Não foi possível extrair o session ID\n";
    }
} else {
    echo "❌ Falha no login\n";
}
?>