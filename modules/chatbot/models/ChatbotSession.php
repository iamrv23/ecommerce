<?php

namespace app\modules\chatbot\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chatbot_sessions".
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $session_id
 * @property int|null $user_id
 * @property string|null $conversation_log
 * @property string $last_activity
 * @property string $created_at
 */
class ChatbotSession extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chatbot_sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tenant_id', 'session_id'], 'required'],
            [['tenant_id', 'user_id'], 'integer'],
            [['conversation_log'], 'safe'],
            [['session_id'], 'string', 'max' => 100],
            [['last_activity', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tenant_id' => 'Tenant ID',
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'conversation_log' => 'Conversation Log',
            'last_activity' => 'Last Activity',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Get or create session
     */
    public static function getOrCreate($sessionId, $tenantId, $userId = null)
    {
        $session = self::find()
            ->where(['session_id' => $sessionId, 'tenant_id' => $tenantId])
            ->one();

        if (!$session) {
            $session = new self();
            $session->tenant_id = $tenantId;
            $session->session_id = $sessionId;
            $session->user_id = $userId;
            $session->conversation_log = json_encode([]);
            $session->save();
        }

        return $session;
    }

    /**
     * Log a message exchange
     */
    public function logMessage($userMessage, $botResponse, $intent = null, $action = null, $result = null)
    {
        $log = json_decode($this->conversation_log, true) ?? [];
        $log[] = [
            'timestamp' => time(),
            'user_message' => $userMessage,
            'bot_response' => $botResponse,
            'intent' => $intent,
            'action' => $action,
            'result' => $result,
        ];
        $this->conversation_log = json_encode($log);
        $this->save();

        // Also log to chatbot_logs table
        $chatbotLog = new ChatbotLog();
        $chatbotLog->tenant_id = $this->tenant_id;
        $chatbotLog->session_id = $this->session_id;
        $chatbotLog->user_message = $userMessage;
        $chatbotLog->bot_response = $botResponse;
        $chatbotLog->intent_detected = $intent;
        $chatbotLog->action_executed = $action;
        $chatbotLog->execution_result = json_encode($result);
        $chatbotLog->save();
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory()
    {
        return json_decode($this->conversation_log, true) ?? [];
    }
}