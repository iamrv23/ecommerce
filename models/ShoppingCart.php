<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ShoppingCart ActiveRecord for table `shopping_cart`.
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 */
class ShoppingCart extends ActiveRecord
{
    public static function tableName()
    {
        return 'shopping_cart';
    }

    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['user_id', 'product_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['session_id'], 'string', 'max' => 100],
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Return associative array productId => quantity for a given user.
     */
    public static function getCartMapForUser($userId)
    {
        $rows = static::find()->where(['user_id' => $userId])->all();
        $map = [];
        foreach ($rows as $r) {
            $map[$r->product_id] = (int)$r->quantity;
        }
        return $map;
    }

    /**
     * Merge given session cart (productId=>quantity) into user's DB cart.
     */
    public static function mergeSessionCartToUser($userId, $sessionCart)
    {
        if (empty($sessionCart) || !$userId) return;
        foreach ($sessionCart as $productId => $qty) {
            $productId = (int)$productId;
            $qty = (int)$qty;
            if ($qty <= 0) continue;
            $existing = static::find()->where(['user_id' => $userId, 'product_id' => $productId])->one();
            $product = Product::findOne($productId);
            $price = $product ? $product->price : 0;
            if ($existing) {
                $existing->quantity = $existing->quantity + $qty;
                $existing->price = $price;
                $existing->save(false);
            } else {
                $m = new static();
                $m->user_id = $userId;
                $m->product_id = $productId;
                $m->quantity = $qty;
                $m->price = $price;
                $m->save(false);
            }
        }
    }

    // Note: session-array sync helper removed — cart is DB-backed (session 'cart' array is deprecated).

    public static function addOrIncrement($userId, $productId, $quantity = 1)
    {
        $product = Product::findOne($productId);
        $price = $product ? $product->price : 0;
        if ($userId) {
            $existing = static::find()->where(['user_id' => $userId, 'product_id' => $productId])->one();
            if ($existing) {
                $existing->quantity += $quantity;
                $existing->price = $price;
                return $existing->save(false);
            }
            $m = new static();
            $m->user_id = $userId;
            $m->product_id = $productId;
            $m->quantity = $quantity;
            $m->price = $price;
            return $m->save(false);
        }
        return false;
    }

    public static function removeForUser($userId, $productId)
    {
        return static::deleteAll(['user_id' => $userId, 'product_id' => $productId]);
    }

    public static function updateQuantitiesForUser($userId, $quantities)
    {
        if (!$userId || empty($quantities)) return;
        foreach ($quantities as $productId => $qty) {
            $productId = (int)$productId;
            $qty = (int)$qty;
            $existing = static::find()->where(['user_id' => $userId, 'product_id' => $productId])->one();
            if ($existing) {
                if ($qty > 0) {
                    $existing->quantity = $qty;
                    $existing->save(false);
                } else {
                    $existing->delete();
                }
            } else {
                if ($qty > 0) {
                    $product = Product::findOne($productId);
                    $m = new static();
                    $m->user_id = $userId;
                    $m->product_id = $productId;
                    $m->quantity = $qty;
                    $m->price = $product ? $product->price : 0;
                    $m->save(false);
                }
            }
        }
    }
}
