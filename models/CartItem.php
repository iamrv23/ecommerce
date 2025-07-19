<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CartItem extends Model
{
    public $product_id;
    public $quantity;
    public $product;

    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity'], 'integer'],
            ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    public function getProduct()
    {
        if ($this->product === null) {
            $this->product = Product::findOne($this->product_id);
        }
        return $this->product;
    }

    public function getSubtotal()
    {
        return $this->getProduct()->price * $this->quantity;
    }
}
