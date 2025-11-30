<?php
use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'User: ' . $model->getDisplayName();
?>
<div class="user-view">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
                <p>
                    <?= Html::a('Back to list', ['index'], ['class' => 'btn btn-secondary']) ?>
                </p>
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'email:email',
                    'first_name',
                    'last_name',
                    [
                        'attribute' => 'role',
                        'value' => $model->getRoleLabel(),
                    ],
                    [
                        'attribute' => 'status',
                        'value' => $model->getStatusLabel(),
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]) ?>
        </div>
    </div>

</div>
