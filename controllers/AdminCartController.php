<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ShoppingCart;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

class AdminCartController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'clear' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // group carts by user_id and session_id (shows guest sessions too)
        $rows = (new \yii\db\Query())
            ->select(['user_id', 'session_id', 'COUNT(*) AS item_count', 'SUM(quantity * price) AS total_amount'])
            ->from('shopping_cart')
            ->groupBy(['user_id', 'session_id'])
            ->orderBy(['user_id' => SORT_DESC])
            ->all();

        $provider = new ArrayDataProvider([
            'allModels' => $rows,
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('index', ['dataProvider' => $provider]);
    }

    public function actionView($userId = null, $sessionId = null)
    {
        $query = ShoppingCart::find()->with('product');
        if ($userId !== null) {
            $query->andWhere(['user_id' => $userId]);
        } else {
            $query->andWhere(['session_id' => $sessionId]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100],
        ]);

        return $this->render('view', ['dataProvider' => $dataProvider, 'userId' => $userId, 'sessionId' => $sessionId]);
    }

    public function actionClear($userId = null, $sessionId = null)
    {
        if ($userId !== null) {
            ShoppingCart::deleteAll(['user_id' => $userId]);
            Yii::$app->session->setFlash('success', 'Cleared carts for user ' . $userId);
        } else if ($sessionId !== null) {
            ShoppingCart::deleteAll(['session_id' => $sessionId]);
            Yii::$app->session->setFlash('success', 'Cleared guest session cart.');
        } else {
            ShoppingCart::deleteAll([]);
            Yii::$app->session->setFlash('success', 'Cleared all carts.');
        }
        return $this->redirect(['index']);
    }
}
