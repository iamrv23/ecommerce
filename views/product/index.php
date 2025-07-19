<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin'): ?>
                <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                }
            ],
            'sku',
            [
                'attribute' => 'price',
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->price);
                }
            ],
            'inventory_quantity',
            'status:boolean',
            'featured:boolean',
            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin',
            ],
        ],
    ]); ?>
</div>
