<?php

namespace app\modules\chatbot\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\modules\chatbot\models\ChatbotIntent;
use app\models\Tenant;

/**
 * Admin controller for chatbot management
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Admin dashboard
     */
    public function actionIndex()
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();

        $intents = ChatbotIntent::find()
            ->where(['tenant_id' => $tenantId])
            ->all();

        $logs = \app\modules\chatbot\models\ChatbotLog::find()
            ->where(['tenant_id' => $tenantId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->all();

        return $this->render('index', [
            'intents' => $intents,
            'logs' => $logs,
        ]);
    }

    /**
     * Manage intents
     */
    public function actionIntents()
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => ChatbotIntent::find()->where(['tenant_id' => $tenantId]),
        ]);

        return $this->render('intents', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create/edit intent
     */
    public function actionIntent($id = null)
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();

        if ($id) {
            $model = ChatbotIntent::findOne(['id' => $id, 'tenant_id' => $tenantId]);
            if (!$model) {
                throw new \yii\web\NotFoundHttpException('Intent not found');
            }
        } else {
            $model = new ChatbotIntent();
            $model->tenant_id = $tenantId;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Intent saved successfully');
            return $this->redirect(['intents']);
        }

        return $this->render('intent', [
            'model' => $model,
        ]);
    }

    /**
     * Delete intent
     */
    public function actionDeleteIntent($id)
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();

        $model = ChatbotIntent::findOne(['id' => $id, 'tenant_id' => $tenantId]);
        if ($model) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Intent deleted successfully');
        }

        return $this->redirect(['intents']);
    }

    /**
     * View logs
     */
    public function actionLogs()
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\modules\chatbot\models\ChatbotLog::find()
                ->where(['tenant_id' => $tenantId])
                ->orderBy(['created_at' => SORT_DESC]),
        ]);

        return $this->render('logs', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Tenant settings
     */
    public function actionSettings()
    {
        $tenantId = \app\modules\chatbot\Module::getTenantId();
        $tenant = Tenant::findOne($tenantId);

        if (!$tenant) {
            throw new \yii\web\NotFoundHttpException('Tenant not found');
        }

        $settings = $tenant->getSettingsArray();

        if (Yii::$app->request->isPost) {
            $newSettings = Yii::$app->request->post('settings', []);
            $tenant->setSettingsArray($newSettings);
            if ($tenant->save()) {
                Yii::$app->session->setFlash('success', 'Settings saved successfully');
                return $this->refresh();
            }
        }

        return $this->render('settings', [
            'tenant' => $tenant,
            'settings' => $settings,
        ]);
    }
}