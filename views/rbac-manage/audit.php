<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'RBAC Audit Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-audit-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-sm table-striped'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'actor_id',
                        'label' => 'Actor',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->actor_id && ($user = \app\models\User::findOne($model->actor_id))) {
                                return Html::a(Html::encode($user->getDisplayName()), ['//user/view', 'id' => $user->id]);
                            }
                            return '<span class="text-muted">system</span>';
                        }
                    ],
                    'action',
                    [
                        'attribute' => 'target',
                        'label' => 'Target',
                        'value' => function($m) { return $m->target; }
                    ],
                    [
                        'attribute' => 'details',
                        'format' => 'ntext',
                        'value' => function($m) { return strlen($m->details) > 200 ? substr($m->details,0,200) . '...' : $m->details; }
                    ],
                    'created_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
