<?php
namespace app\components;

use Yii;
use yii\base\Event;
use yii\web\User as WebUser;

/**
 * Handles cart merge on login and guest cart persistence.
 */
class CartEventHandler
{
    /**
     * Called after user login.
     * Merge session cart and any session-bound DB cart into user's cart.
     */
    public static function onAfterLogin($event)
    {
        $user = $event->identity;
        if (!$user || !isset($user->id)) return;
        $userId = $user->id;
        // Merge any cart rows saved under this session_id into user.
        // No session-array is used for logged-in users; guest rows are persisted under session_id.
        $sessionId = session_id();
        if ($sessionId) {
            $rows = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId])->all();
            foreach ($rows as $r) {
                \app\models\ShoppingCart::addOrIncrement($userId, $r->product_id, $r->quantity);
                $r->delete();
            }
        }
    }

    /**
     * Persist guest cart to DB under session_id so it can be merged on login/signup.
     * @param array $cart associative productId=>quantity
     */
    public static function persistGuestCartToDb($cart)
    {
        if (empty($cart)) return;
        $sessionId = session_id();
        if (!$sessionId) return;

        foreach ($cart as $productId => $qty) {
            $productId = (int)$productId;
            $qty = (int)$qty;
            if ($qty <= 0) continue;
            $existing = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId, 'product_id' => $productId])->one();
            $product = \app\models\Product::findOne($productId);
            $price = $product ? $product->price : 0;
            if ($existing) {
                $existing->quantity += $qty;
                $existing->price = $price;
                $existing->save(false);
            } else {
                $m = new \app\models\ShoppingCart();
                $m->session_id = $sessionId;
                $m->product_id = $productId;
                $m->quantity = $qty;
                $m->price = $price;
                $m->save(false);
            }
        }
    }
}
