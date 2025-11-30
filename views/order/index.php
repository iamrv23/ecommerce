<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
</div>
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mb-4"><?= Html::encode($this->title) ?></h1>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-striped table-hover'],
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'total_amount',
                            'value' => function($model) {
                                return Yii::$app->formatter->asCurrency($model->total_amount);
                            }
                        ],
                        'status',
                        'created_at:datetime',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $model) {
                                    return Html::a('View', $url, ['class' => 'btn btn-sm btn-primary']);
                                }
                            ]
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
