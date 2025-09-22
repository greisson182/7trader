<?php
session_start();
header("Content-Type: text/plain");
echo "=== DADOS DA SESSÃO ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "\n=== COOKIES ===\n";
print_r($_COOKIE);
?>