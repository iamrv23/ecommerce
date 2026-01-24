<?php

namespace app\modules\chatbot\services;

use Yii;
use yii\base\Component;

/**
 * NLP Service using ChatterBot (Python-based)
 */
class NlpService extends Component
{
    public $pythonExecutable = '/Applications/MAMP/htdocs/ecommerce/rasa/rasa_env/bin/python3.9';
    public $chatbotScript = '/Applications/MAMP/htdocs/ecommerce/rasa/chatbot.py';
    public $timeout = 30;

    /**
     * Parse message to detect intent and entities
     */
    public function parseMessage($message, $sender = null)
    {
        try {
            $command = escapeshellcmd($this->pythonExecutable) . ' ' .
                      escapeshellarg($this->chatbotScript) . ' ' .
                      escapeshellarg($message);

            if ($sender) {
                $command .= ' ' . escapeshellarg($sender);
            }

            $output = shell_exec($command . ' 2>&1');

            if ($output === null) {
                Yii::error('Chatbot command execution failed', __METHOD__);
                return null;
            }

            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Yii::error('Invalid JSON response from chatbot: ' . $output, __METHOD__);
                return null;
            }

            if (isset($result['error'])) {
                Yii::error('Chatbot error: ' . $result['error'], __METHOD__);
                return null;
            }

            return [
                'intent' => $result['intent'] ?? null,
                'confidence' => $result['confidence'] ?? 0,
                'entities' => $result['entities'] ?? [], // Use entities from Python response
                'text' => $result['text'] ?? $message,
            ];

        } catch (\Exception $e) {
            Yii::error('Chatbot execution error: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Send message to chatbot and get response
     */
    public function converse($message, $sender = null)
    {
        $result = $this->parseMessage($message, $sender);

        if ($result && isset($result['intent'])) {
            // Return a simple response based on intent
            $responses = [
                'create_customer' => 'I can help you create a new customer account. What information do you have?',
                'process_order' => 'I can assist with processing an order. What would you like to order?',
                'check_inventory' => 'I can check inventory levels for you. What product are you looking for?',
                'assign_license' => 'I can help assign licenses. What type of license do you need?',
                'generate_report' => 'I can generate reports for you. What type of report would you like?',
                'automate_workflow' => 'I can help automate workflows. What process would you like to automate?',
            ];

            return $responses[$result['intent']] ?? 'I\'m sorry, I didn\'t understand that request.';
        }

        return 'Sorry, I\'m having trouble processing your request. Please try again.';
    }

    /**
     * Train the chatbot model (if supported)
     */
    public function trainModel()
    {
        // Training happens automatically when the script runs
        // For now, return true
        return true;
    }

    /**
     * Check if chatbot is available
     */
    public function isAvailable()
    {
        try {
            $command = escapeshellcmd($this->pythonExecutable) . ' --version 2>&1';
            $output = shell_exec($command);

            return strpos($output, 'Python') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}