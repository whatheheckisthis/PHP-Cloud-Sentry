<?php
header('Content-Type: text/plain');

echo "PHP-Cloud-Sentry production container is running.\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
