<?php

// Define Yii constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', YII_ENV === 'dev');

// Include Yii
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';
$app = new yii\web\Application($config);

use app\modules\chatbot\services\ChatbotService;

try {
    // Set tenant context - not needed since getTenantId() returns 1 by default
    // \app\modules\chatbot\Module::setTenantId(1);

    $service = new ChatbotService(1);

    // Test cases
    $testCases = [
        'check inventory of iPhone 15 Pro',
        'check stock for product SKU-123',
        'check inventory', // Should return "Product name or SKU is required"
        'how many Samsung Galaxy S24 do we have',
        'check inventory of SAM24001', // Test valid SKU
    ];

    foreach ($testCases as $message) {
        echo "Testing: \"$message\"\n";
        $result = $service->processMessage([
            'message' => $message,
            'session_id' => 'test_session_' . time()
        ]);

        echo "Result: " . $result['message'] . "\n";
        echo "Intent: " . ($result['intent'] ?? 'none') . "\n";
        echo "Confidence: " . ($result['confidence'] ?? 0) . "\n";
        echo "---\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}