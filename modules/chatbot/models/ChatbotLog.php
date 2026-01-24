<?php

namespace app\modules\chatbot\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chatbot_logs".
 *
 * @property int $id
 * @property int $tenant_id
 * @property string|null $session_id
 * @property string|null $user_message
 * @property string|null $bot_response
 * @property string|null $intent_detected
 * @property string|null $action_executed
 * @property string|null $execution_result
 * @property string $created_at
 */
class ChatbotLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chatbot_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tenant_id'], 'required'],
            [['tenant_id'], 'integer'],
            [['user_message', 'bot_response', 'execution_result'], 'safe'],
            [['session_id', 'intent_detected', 'action_executed'], 'string', 'max' => 100],
            [['created_at'], 'safe'],
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
            'user_message' => 'User Message',
            'bot_response' => 'Bot Response',
            'intent_detected' => 'Intent Detected',
            'action_executed' => 'Action Executed',
            'execution_result' => 'Execution Result',
            'created_at' => 'Created At',
        ];
    }
}