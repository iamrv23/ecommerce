<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = $userId === null ? 'Guest carts' : 'Cart for user ' . $userId;
$this->params['breadcrumbs'][] = ['label' => 'Shopping Carts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-cart-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Clear', ['clear', 'userId' => $userId], ['class' => 'btn btn-danger ms-2', 'data' => ['confirm' => 'Clear this cart?']]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'product_id',
                'value' => function($m) { return isset($m->product) ? $m->product->name : 'Product #' . $m->product_id; }
            ],
            'quantity',
            [
                'attribute' => 'price',
                'value' => function($m) { return Yii::$app->formatter->asCurrency($m->price); }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime'
            ],
        ]
    ]) ?>
</div>
