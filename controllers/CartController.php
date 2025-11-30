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
        $cartItems = [];
        $total = 0;

        if (!Yii::$app->user->isGuest) {
            // load cart items from DB by user_id
            $userId = Yii::$app->user->id;
            $rows = \app\models\ShoppingCart::find()->where(['user_id' => $userId])->with('product')->all();
            foreach ($rows as $r) {
                if ($r->product) {
                    $cartItem = new CartItem([
                        'product_id' => $r->product_id,
                        'quantity' => (int)$r->quantity,
                        'product' => $r->product,
                    ]);
                    $cartItems[] = $cartItem;
                    $total += $cartItem->getSubtotal();
                }
            }
        } else {
            // guest: load cart items persisted under session_id
            $sessionId = session_id();
            if ($sessionId) {
                $rows = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId])->with('product')->all();
                foreach ($rows as $r) {
                    if ($r->product) {
                        $cartItem = new CartItem([
                            'product_id' => $r->product_id,
                            'quantity' => (int)$r->quantity,
                            'product' => $r->product,
                        ]);
                        $cartItems[] = $cartItem;
                        $total += $cartItem->getSubtotal();
                    }
                }
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
        if (Yii::$app->user->isGuest) {
            $sessionId = session_id();
            if (!$sessionId) {
                Yii::$app->session->setFlash('error', 'Session is not available.');
                return $this->redirect(['product/view', 'id' => $id]);
            }
            $existing = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId, 'product_id' => $id])->one();
            if ($existing) {
                $newQty = $existing->quantity + 1;
                if ($newQty > $product->inventory_quantity) {
                    Yii::$app->session->setFlash('error', 'Not enough stock available.');
                    return $this->redirect(['product/view', 'id' => $id]);
                }
                $existing->quantity = $newQty;
                $existing->price = $product->price;
                $existing->save(false);
            } else {
                if (1 > $product->inventory_quantity) {
                    Yii::$app->session->setFlash('error', 'Not enough stock available.');
                    return $this->redirect(['product/view', 'id' => $id]);
                }
                $m = new \app\models\ShoppingCart();
                $m->session_id = $sessionId;
                $m->product_id = $id;
                $m->quantity = 1;
                $m->price = $product->price;
                $m->save(false);
            }
        } else {
            $userId = Yii::$app->user->id;
            // validate against inventory
            $current = \app\models\ShoppingCart::find()->where(['user_id' => $userId, 'product_id' => $id])->one();
            $currentQty = $current ? $current->quantity : 0;
            if (($currentQty + 1) > $product->inventory_quantity) {
                Yii::$app->session->setFlash('error', 'Not enough stock available.');
                return $this->redirect(['product/view', 'id' => $id]);
            }
            \app\models\ShoppingCart::addOrIncrement($userId, $id, 1);
        }
        Yii::$app->session->setFlash('success', 'Product added to cart.');
        return $this->redirect(['index']);
    }

    public function actionRemove($id)
    {
        if (!Yii::$app->user->isGuest) {
            \app\models\ShoppingCart::removeForUser(Yii::$app->user->id, $id);
        } else {
            // remove any guest row tied to this session
            $sessionId = session_id();
            if ($sessionId) {
                \app\models\ShoppingCart::deleteAll(['session_id' => $sessionId, 'product_id' => $id]);
            }
        }
        Yii::$app->session->setFlash('success', 'Product removed from cart.');

        return $this->redirect(['index']);
    }

    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $quantities = Yii::$app->request->post('quantities', []);
            // Update DB-backed cart entries only
            if (!Yii::$app->user->isGuest) {
                // validate quantities against product inventory before applying
                $safe = [];
                foreach ($quantities as $productId => $quantity) {
                    $product = Product::findOne($productId);
                    $q = (int)$quantity;
                    if ($q <= 0) continue;
                    if ($product && $q <= $product->inventory_quantity) {
                        $safe[$productId] = $q;
                    }
                }
                if (!empty($safe)) {
                    \app\models\ShoppingCart::updateQuantitiesForUser(Yii::$app->user->id, $safe);
                }
            } else {
                $sessionId = session_id();
                if ($sessionId) {
                    foreach ($quantities as $pid => $q) {
                        $pid = (int)$pid;
                        $q = (int)$q;
                        $product = Product::findOne($pid);
                        if (!$product) continue;
                        if ($q > 0) {
                            if ($q > $product->inventory_quantity) continue;
                            $row = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId, 'product_id' => $pid])->one();
                            if ($row) {
                                $row->quantity = $q;
                                $row->save(false);
                            } else {
                                $m = new \app\models\ShoppingCart();
                                $m->session_id = $sessionId;
                                $m->product_id = $pid;
                                $m->quantity = $q;
                                $m->price = $product->price;
                                $m->save(false);
                            }
                        } else {
                            \app\models\ShoppingCart::deleteAll(['session_id' => $sessionId, 'product_id' => $pid]);
                        }
                    }
                }
            }
            Yii::$app->session->setFlash('success', 'Cart updated successfully.');
        }

        return $this->redirect(['index']);
    }
}
