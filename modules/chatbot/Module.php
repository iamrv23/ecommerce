<?php

namespace app\modules\chatbot;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Chatbot module for conversational AI platform
 */
class Module extends BaseModule
{
    public $controllerNamespace = 'app\modules\chatbot\controllers';

    public function init()
    {
        parent::init();

        // Set module-specific aliases
        Yii::setAlias('@chatbot', __DIR__);

        // Register translations if needed
        // $this->registerTranslations();
    }

    /**
     * Get current tenant ID
     */
    public static function getTenantId()
    {
        // For now, return default tenant
        // In production, implement proper tenant resolution
        return 1;
    }

    /**
     * Get tenant-specific chatbot settings
     */
    public static function getTenantSettings($tenantId = null)
    {
        if ($tenantId === null) {
            $tenantId = self::getTenantId();
        }

        $tenant = \app\models\Tenant::findOne($tenantId);
        if (!$tenant) {
            throw new \yii\web\NotFoundHttpException('Tenant not found');
        }

        return $tenant->settings ?? [];
    }
}