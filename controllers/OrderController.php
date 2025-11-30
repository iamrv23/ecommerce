<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\OrderItem;
use app\models\Product;
use app\models\ShoppingCart;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class OrderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Order::find();
        if (Yii::$app->user->identity->role !== 'admin') {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $order = $this->findModel($id);
        if (Yii::$app->user->identity->role !== 'admin' && $order->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('The requested order does not exist.');
        }

        return $this->render('view', [
            'model' => $order,
        ]);
    }

    public function actionCreate()
    {
        $userId = Yii::$app->user->id;
        $rows = ShoppingCart::find()->where(['user_id' => $userId])->with('product')->all();
        if (empty($rows)) {
            Yii::$app->session->setFlash('error', 'Your cart is empty.');
            return $this->redirect(['cart/index']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            $order->user_id = Yii::$app->user->id;
            $order->status = Order::STATUS_NEW;
            $order->total_amount = 0;

            if ($order->save()) {
                $totalAmount = 0;
                foreach ($rows as $row) {
                    $product = $row->product ?: Product::findOne($row->product_id);
                    $quantity = (int)$row->quantity;
                    if ($product && $product->inventory_quantity >= $quantity) {
                        $orderItem = new OrderItem([
                            'order_id' => $order->id,
                            'product_id' => $row->product_id,
                            'quantity' => $quantity,
                            'unit_price' => $product->price,
                        ]);

                        if ($orderItem->save()) {
                            $totalAmount += $orderItem->getSubtotal();
                            $product->inventory_quantity -= $quantity;
                            $product->save(false);
                        }
                    }
                }

                $order->total_amount = $totalAmount;
                if ($order->save()) {
                    $transaction->commit();
                    // clear user's shopping cart rows
                    ShoppingCart::deleteAll(['user_id' => $userId]);
                    Yii::$app->session->setFlash('success', 'Order created successfully.');
                    return $this->redirect(['view', 'id' => $order->id]);
                }
            }

            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'There was an error creating your order.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'There was an error creating your order.');
        }

        return $this->redirect(['cart/index']);
    }

    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested order does not exist.');
    }
}
