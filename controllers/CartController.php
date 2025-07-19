<?php

namespace app\controllers;

use Yii;
use app\models\CartItem;
use app\models\Product;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CartController extends Controller
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
                    'add' => ['post'],
                    'remove' => ['post'],
                    'update' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $cart = Yii::$app->session->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::findOne($productId);
            if ($product) {
                $cartItem = new CartItem([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'product' => $product,
                ]);
                $cartItems[] = $cartItem;
                $total += $cartItem->getSubtotal();
            }
        }

        return $this->render('index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function actionAdd($id)
    {
        $product = Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException('Product not found.');
        }

        $cart = Yii::$app->session->get('cart', []);
        $quantity = isset($cart[$id]) ? $cart[$id] + 1 : 1;

        if ($quantity > $product->inventory_quantity) {
            Yii::$app->session->setFlash('error', 'Not enough stock available.');
            return $this->redirect(['product/view', 'id' => $id]);
        }

        $cart[$id] = $quantity;
        Yii::$app->session->set('cart', $cart);
        Yii::$app->session->setFlash('success', 'Product added to cart.');

        return $this->redirect(['index']);
    }

    public function actionRemove($id)
    {
        $cart = Yii::$app->session->get('cart', []);
        unset($cart[$id]);
        Yii::$app->session->set('cart', $cart);
        Yii::$app->session->setFlash('success', 'Product removed from cart.');

        return $this->redirect(['index']);
    }

    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $quantities = Yii::$app->request->post('quantities', []);
            $cart = Yii::$app->session->get('cart', []);

            foreach ($quantities as $productId => $quantity) {
                if ($quantity > 0) {
                    $product = Product::findOne($productId);
                    if ($product && $quantity <= $product->inventory_quantity) {
                        $cart[$productId] = (int)$quantity;
                    }
                } else {
                    unset($cart[$productId]);
                }
            }

            Yii::$app->session->set('cart', $cart);
            Yii::$app->session->setFlash('success', 'Cart updated successfully.');
        }

        return $this->redirect(['index']);
    }
}
