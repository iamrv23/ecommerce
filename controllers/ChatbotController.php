<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Legacy ChatbotController - Redirects to new chatbot module
 * @deprecated Use the chatbot module instead
 */
class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

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
     * Handle chatbot messages - redirects to new module
     *
     * @return array
     */
    public function actionMessage()
    {
        // Redirect to new chatbot module
        $service = new \app\modules\chatbot\services\ChatbotService();
        return $service->processMessage(Yii::$app->request->post());
    }
}
