<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class OrderItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'order_items';
    }

    public function rules()
    {
        return [
            [['order_id', 'product_id', 'quantity', 'unit_price'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            [['unit_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'product_id' => 'Product',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getSubtotal()
    {
        return $this->unit_price * $this->quantity;
    }
}
