<?php

namespace app\modules\chatbot\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chatbot_intents".
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $intent_name
 * @property string $action_type
 * @property string $action_config
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class ChatbotIntent extends ActiveRecord
{
    const ACTION_AUTOMATION = 'automation';
    const ACTION_QUERY = 'query';
    const ACTION_RESPONSE = 'response';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chatbot_intents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tenant_id', 'intent_name', 'action_type'], 'required'],
            [['tenant_id'], 'integer'],
            [['action_config'], 'safe'],
            [['is_active'], 'boolean'],
            [['intent_name'], 'string', 'max' => 100],
            [['action_type'], 'in', 'range' => [self::ACTION_AUTOMATION, self::ACTION_QUERY, self::ACTION_RESPONSE]],
            [['created_at', 'updated_at'], 'safe'],
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
            'intent_name' => 'Intent Name',
            'action_type' => 'Action Type',
            'action_config' => 'Action Config',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find intent by name and tenant
     */
    public static function findByIntent($intentName, $tenantId)
    {
        return self::find()
            ->where(['intent_name' => $intentName, 'tenant_id' => $tenantId, 'is_active' => true])
            ->one();
    }

    /**
     * Get action config as array
     */
    public function getActionConfigArray()
    {
        return json_decode($this->action_config, true) ?? [];
    }
}