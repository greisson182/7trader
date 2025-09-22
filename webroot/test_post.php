<?php
echo "=== TEST POST REQUEST ===\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "POST data:\n";
print_r($_POST);
echo "Raw input:\n";
echo file_get_contents('php://input') . "\n";
?>