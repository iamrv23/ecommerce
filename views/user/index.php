<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
?>
<div class="user-index">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'id',
                    'username',
                    'email:email',
                    [
                        'attribute' => 'role',
                        'value' => function($model) { return $model->getRoleLabel(); }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($model) { return $model->getStatusLabel(); }
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                ],
            ]) ?>
        </div>
    </div>

</div>
