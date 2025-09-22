<?php
echo "=== TESTE DO REDIRECIONAMENTO CORRIGIDO ===\n";

// Teste 1: Login do estudante
echo "\n1. Fazendo login como estudante...\n";
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
        
        // Teste 2: Acessar a página inicial (/) para verificar o redirecionamento
        echo "\n2. Testando redirecionamento da página inicial...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Status da página inicial: $httpCode\n";
        
        if ($httpCode == 302) {
            if (preg_match('/Location: (.+)/', $response, $matches)) {
                $location = trim($matches[1]);
                echo "Redirecionando para: $location\n";
                
                if ($location === '/admin/students/dashboard/') {
                    echo "✅ Redirecionamento correto!\n";
                    
                    // Teste 3: Verificar se o dashboard funciona
                    echo "\n3. Testando acesso ao dashboard...\n";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/students/dashboard/');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    echo "Status do dashboard: $httpCode\n";
                    
                    if ($httpCode == 200) {
                        echo "✅ Dashboard funcionando perfeitamente!\n";
                        echo "✅ PROBLEMA RESOLVIDO COMPLETAMENTE!\n";
                    } else {
                        echo "❌ Dashboard não está funcionando (status: $httpCode)\n";
                    }
                } else {
                    echo "❌ Redirecionamento incorreto. Esperado: /admin/students/dashboard/\n";
                }
            }
        } else {
            echo "❌ Não houve redirecionamento da página inicial\n";
        }
        
    } else {
        echo "❌ Não foi possível extrair o session ID\n";
    }
} else {
    echo "❌ Falha no login (status: $httpCode)\n";
}
?>