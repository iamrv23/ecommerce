<?php

namespace app\modules\chatbot\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\chatbot\services\ChatbotService;

/**
 * API Controller for chatbot interactions
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Remove rate limiter for API
        unset($behaviors['rateLimiter']);

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return true;
        }
        return false;
    }

    /**
     * Handle chatbot messages
     * POST /chatbot/api/message
     */
    public function actionMessage()
    {
        $params = Yii::$app->request->post();
        $tenantId = $params['tenant_id'] ?? \app\modules\chatbot\Module::getTenantId();

        $service = new ChatbotService($tenantId);
        return $service->processMessage($params);
    }

    /**
     * Direct automation execution
     * POST /chatbot/api/automate
     */
    public function actionAutomate()
    {
        $params = Yii::$app->request->post();
        $action = $params['action'] ?? null;
        $actionParams = $params['params'] ?? [];
        $tenantId = $params['tenant_id'] ?? \app\modules\chatbot\Module::getTenantId();

        if (!$action) {
            return [
                'success' => false,
                'message' => 'Action parameter is required',
            ];
        }

        try {
            $automationService = new \app\modules\chatbot\services\AutomationService($tenantId);
            $result = $automationService->execute($action, $actionParams);

            return [
                'success' => true,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get conversation history
     * GET /chatbot/api/history?session_id=xxx
     */
    public function actionHistory()
    {
        $sessionId = Yii::$app->request->get('session_id');
        $tenantId = Yii::$app->request->get('tenant_id', \app\modules\chatbot\Module::getTenantId());

        if (!$sessionId) {
            return [
                'success' => false,
                'message' => 'Session ID is required',
            ];
        }

        $session = \app\modules\chatbot\models\ChatbotSession::find()
            ->where(['session_id' => $sessionId, 'tenant_id' => $tenantId])
            ->one();

        if (!$session) {
            return [
                'success' => false,
                'message' => 'Session not found',
            ];
        }

        return [
            'success' => true,
            'history' => $session->getConversationHistory(),
        ];
    }

    /**
     * Check chatbot availability
     * GET /chatbot/api/status
     */
    public function actionStatus()
    {
        $tenantId = Yii::$app->request->get('tenant_id', \app\modules\chatbot\Module::getTenantId());
        $service = new ChatbotService($tenantId);

        return [
            'available' => $service->isAvailable(),
            'tenant_id' => $tenantId,
        ];
    }
}