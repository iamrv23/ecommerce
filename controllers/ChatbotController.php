<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

/**
 * ChatbotController implements a simple AI chatbot using OpenAI API.
 */
class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Disable authentication for simplicity (add your own auth if needed).
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Disable rate limiter for API (optional)
        unset($behaviors['rateLimiter']);

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return true;
        }
        return false;
    }

    /**
     * Handle chatbot messages.
     *
     * @return array
     */
    public function actionMessage()
    {
        $params = Yii::$app->request->post();
        $message = $params['message'] ?? null;
        $sessionId = $params['session_id'] ?? Yii::$app->session->id;

        if (!$message) {
            return [
                'success' => false,
                'message' => 'Message cannot be empty.',
            ];
        }

        // Load chatbot settings
        $chatbotSettings = Yii::$app->params['chatbot'];
        if (!$chatbotSettings['enabled']) {
            return [
                'success' => false,
                'message' => 'Chatbot is currently disabled. Please try again later.',
            ];
        }

        // Initialize conversation
        $conversation = Yii::$app->cache->getOrSet("chatbot_conversation_{$sessionId}", function () {
            return [];
        }, $chatbotSettings['session_timeout']);

        // Append user message
        $conversation[] = [
            'role' => 'user',
            'content' => $message,
        ];

        // Trim conversation to max length
        if (count($conversation) > $chatbotSettings['max_conversation_length']) {
            $conversation = array_slice($conversation, -$chatbotSettings['max_conversation_length']);
        }

        // Prepare OpenAI API request
        $apiKey = $chatbotSettings['api_key'];
        $openAiUrl = 'https://api.openai.com/v1/chat/completions';

        $postData = [
            'model' => $chatbotSettings['model'],
            'messages' => array_merge([
                [
                    'role' => 'system',
                    'content' => $chatbotSettings['system_prompt'],
                ],
            ], $conversation),
            'max_tokens' => $chatbotSettings['max_tokens'],
            'temperature' => $chatbotSettings['temperature'],
        ];

        // Send request to OpenAI API
        $ch = curl_init($openAiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return [
                'success' => false,
                'message' => 'Failed to connect to chatbot service. Please try again later.',
            ];
        }

        $responseData = json_decode($response, true);
        if (!isset($responseData['choices'][0]['message']['content'])) {
            return [
                'success' => false,
                'message' => $chatbotSettings['fallback_message'],
            ];
        }

        $botMessage = trim($responseData['choices'][0]['message']['content']);

        // Append bot response
        $conversation[] = [
            'role' => 'assistant',
            'content' => $botMessage,
        ];

        // Save conversation to cache
        Yii::$app->cache->set("chatbot_conversation_{$sessionId}", $conversation, $chatbotSettings['session_timeout']);

        return [
            'success' => true,
            'message' => $botMessage,
            'session_id' => $sessionId,
        ];
    }
}
