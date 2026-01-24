<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
// Use environment variables or override for testing
$db['dsn'] = getenv('TEST_DB_DSN') ?: 'mysql:host=localhost;dbname=ecommerce_test';
$db['username'] = getenv('TEST_DB_USER') ?: 'root';
$db['password'] = getenv('TEST_DB_PASSWORD') ?: '';

return $db;
