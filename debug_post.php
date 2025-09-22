<?php
echo "=== DEBUG POST REQUEST ===\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";
echo "Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'Not set') . "\n";
echo "\nPOST data:\n";
print_r($_POST);
echo "\nRaw input:\n";
echo file_get_contents('php://input');
echo "\n\nAll SERVER vars:\n";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0 || in_array($key, ['REQUEST_METHOD', 'REQUEST_URI', 'CONTENT_TYPE', 'CONTENT_LENGTH'])) {
        echo "$key: $value\n";
    }
}
?>