<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Order extends ActiveRecord
{
    const STATUS_NEW = 'new';
    const STATUS_PAID = 'paid';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return 'orders';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'total_amount', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['total_amount'], 'number'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [
                self::STATUS_NEW,
                self::STATUS_PAID,
                self::STATUS_SHIPPED,
                self::STATUS_COMPLETED,
                self::STATUS_CANCELLED,
            ]],
            [['shipping_address', 'billing_address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'shipping_address' => 'Shipping Address',
            'billing_address' => 'Billing Address',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
}
