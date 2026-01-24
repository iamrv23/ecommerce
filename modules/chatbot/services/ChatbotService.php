<?php

namespace app\modules\chatbot\services;

use Yii;
use yii\base\Component;
use app\modules\chatbot\models\ChatbotSession;
use app\modules\chatbot\models\ChatbotIntent;

/**
 * Main Chatbot Service
 */
class ChatbotService extends Component
{
    private $tenantId;
    private $nlpService;
    private $automationService;

    public function __construct($tenantId = null, $config = [])
    {
        $this->tenantId = $tenantId ?: \app\modules\chatbot\Module::getTenantId();
        $this->nlpService = new NlpService();
        $this->automationService = new AutomationService($this->tenantId);
        parent::__construct($config);
    }

    /**
     * Process a user message
     */
    public function processMessage($data)
    {
        $message = $data['message'] ?? '';
        $sessionId = $data['session_id'] ?? Yii::$app->session->id;
        $userId = $data['user_id'] ?? (Yii::$app->user->isGuest ? null : Yii::$app->user->id);

        if (empty($message)) {
            return [
                'success' => false,
                'message' => 'Message cannot be empty',
            ];
        }

        // Get or create session
        $session = ChatbotSession::getOrCreate($sessionId, $this->tenantId, $userId);

        // Parse message with NLP
        $parsed = $this->nlpService->parseMessage($message, $sessionId);

        if (!$parsed) {
            // Fallback response
            $response = 'I\'m sorry, I\'m having trouble understanding you right now. Please try again later.';
            $session->logMessage($message, $response);
            return [
                'success' => true,
                'message' => $response,
                'session_id' => $sessionId,
            ];
        }

        $intent = $parsed['intent'];
        $confidence = $parsed['confidence'];
        $entities = $parsed['entities'];

        // Check if intent maps to automation
        $intentModel = ChatbotIntent::findByIntent($intent, $this->tenantId);

        if ($intentModel && $confidence > 0.7) { // Confidence threshold
            $result = $this->executeAutomation($intentModel, $entities);

            // Format response based on result
            if ($result['success']) {
                if ($intent === 'check_inventory' && isset($result['product'])) {
                    $product = $result['product'];
                    $response = "Product '{$product['name']}' (SKU: {$product['sku']}) has {$product['inventory_quantity']} units in stock. Price: $" . number_format($product['price'], 2);
                } else {
                    $response = $result['message'] ?? 'Action completed successfully';
                }
            } else {
                $response = $result['message'] ?? 'Sorry, I couldn\'t complete that action';
            }

            $session->logMessage($message, $response, $intent, $intentModel->action_type, $result);
        } else {
            // Use Rasa for conversational response
            $response = $this->nlpService->converse($message, $sessionId);
            $session->logMessage($message, $response, $intent);
        }

        return [
            'success' => true,
            'message' => $response,
            'session_id' => $sessionId,
            'intent' => $intent,
            'confidence' => $confidence,
        ];
    }

    /**
     * Execute automation based on intent
     */
    private function executeAutomation($intentModel, $entities)
    {
        $config = $intentModel->getActionConfigArray();
        $service = $config['service'] ?? null;
        $method = $config['method'] ?? null;

        if (!$service || !$method) {
            return [
                'success' => false,
                'message' => 'Invalid automation configuration',
            ];
        }

        // Extract parameters from entities
        $params = [];
        foreach ($entities as $entity) {
            $params[$entity['entity']] = $entity['value'];
        }

        try {
            if ($service === 'AutomationService') {
                $result = $this->automationService->execute($method, $params);
            } else {
                // Could extend to other services
                throw new \Exception("Service '{$service}' not supported");
            }

            return $result;
        } catch (\Exception $e) {
            Yii::error('Automation execution failed: ' . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'Sorry, I encountered an error while processing your request.',
            ];
        }
    }

    /**
     * Check if chatbot is available
     */
    public function isAvailable()
    {
        return $this->nlpService->isAvailable();
    }
}