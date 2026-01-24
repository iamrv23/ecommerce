<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tenants".
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $settings JSON settings
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Tenant extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tenants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'domain'], 'required'],
            [['settings'], 'safe'],
            [['is_active'], 'boolean'],
            [['name', 'domain'], 'string', 'max' => 100],
            [['domain'], 'unique'],
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
            'name' => 'Name',
            'domain' => 'Domain',
            'settings' => 'Settings',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get settings as array
     */
    public function getSettingsArray()
    {
        return json_decode($this->settings, true) ?? [];
    }

    /**
     * Set settings from array
     */
    public function setSettingsArray($settings)
    {
        $this->settings = json_encode($settings);
    }
}