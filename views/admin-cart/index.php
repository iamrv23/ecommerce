<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Shopping Carts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-cart-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Clear guest carts', ['clear', 'userId' => null], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Clear all guest carts?']]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'user_id',
                'label' => 'User',
                'value' => function($m) {
                    return $m['user_id'] === null ? 'Guest (session)' : Html::a('User #'.$m['user_id'], ['view', 'userId' => $m['user_id']]);
                },
                'format' => 'raw'
            ],
            'item_count',
            [
                'attribute' => 'total_amount',
                'label' => 'Total',
                'value' => function($m) { return Yii::$app->formatter->asCurrency($m['total_amount']); }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {clear}',
                'buttons' => [
                    'view' => function($url, $model) { return Html::a('View', ['view', 'userId' => $model['user_id']]); },
                    'clear' => function($url, $model) { return Html::a('Clear', ['clear', 'userId' => $model['user_id']], ['class' => 'text-danger', 'data' => ['confirm' => 'Clear this user cart?']]); },
                ]
            ]
        ]
    ]) ?>
</div>
