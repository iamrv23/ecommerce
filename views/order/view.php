<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Order #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">
</div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
                <p>
                    <?= Html::a('Back to list', ['index'], ['class' => 'btn btn-secondary']) ?>
                </p>
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'total_amount',
                        'value' => function($model) {
                            return Yii::$app->formatter->asCurrency($model->total_amount);
                        }
                    ],
                    'status',
                    'shipping_address:ntext',
                    'billing_address:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

    <h2>Order Items</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->orderItems as $item): ?>
                    <tr>
                        <td><?= Html::a(Html::encode($item->product->name), ['product/view', 'id' => $item->product_id]) ?></td>
                        <td><?= Yii::$app->formatter->asCurrency($item->unit_price) ?></td>
                        <td><?= $item->quantity ?></td>
                        <td><?= Yii::$app->formatter->asCurrency($item->getSubtotal()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong><?= Yii::$app->formatter->asCurrency($model->total_amount) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
