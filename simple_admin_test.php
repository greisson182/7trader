<?php
session_start();

// Simular login do estudante
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'greisson';
$_SESSION['role'] = 'student';

echo "Sessão criada para estudante:\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "Username: " . $_SESSION['username'] . "\n";
echo "Role: " . $_SESSION['role'] . "\n";
echo "Session ID: " . session_id() . "\n\n";

// Testar usando Invoke-WebRequest do PowerShell
$sessionId = session_id();
echo "Para testar o acesso, execute no PowerShell:\n";
echo "Invoke-WebRequest -Uri 'http://localhost:8000/admin' -Headers @{'Cookie'='PHPSESSID=$sessionId'} | Select-Object StatusCode, Headers\n\n";

// Salvar session ID em arquivo para usar no PowerShell
file_put_contents('session_id.txt', $sessionId);
echo "Session ID salvo em session_id.txt\n";
?>