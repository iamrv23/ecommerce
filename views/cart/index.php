<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $cartItems app\models\CartItem[] */
/* @var $total float */

$this->title = 'Shopping Cart';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($cartItems)): ?>
        <?= Html::beginForm(['cart/update'], 'post') ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?= Html::a(Html::encode($item->product->name), ['product/view', 'id' => $item->product_id]) ?>
                            </td>
                            <td><?= Yii::$app->formatter->asCurrency($item->product->price) ?></td>
                            <td>
                                <?= Html::input('number', "quantities[{$item->product_id}]", $item->quantity, [
                                    'min' => 1,
                                    'max' => $item->product->inventory_quantity,
                                    'class' => 'form-control',
                                    'style' => 'width: 80px',
                                ]) ?>
                            </td>
                            <td><?= Yii::$app->formatter->asCurrency($item->getSubtotal()) ?></td>
                            <td>
                                <?= Html::a('Remove', ['cart/remove', 'id' => $item->product_id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Are you sure you want to remove this item?',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td><strong><?= Yii::$app->formatter->asCurrency($total) ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= Html::submitButton('Update Cart', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Continue Shopping', ['product/index'], ['class' => 'btn btn-default']) ?>
            </div>
            <div class="col-md-6 text-right">
                <?= Html::a('Checkout', ['order/create'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <?= Html::endForm() ?>
    <?php else: ?>
        <div class="alert alert-info">
            Your cart is empty. <?= Html::a('Continue shopping', ['product/index']) ?>
        </div>
    <?php endif; ?>
</div>
