<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">
    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin'): ?>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'slug',
                    'description:ntext',
                    'short_description:ntext',
                    'sku',
                    [
                        'attribute' => 'price',
                        'value' => function($model) {
                            return Yii::$app->formatter->asCurrency($model->price);
                        }
                    ],
                    [
                        'attribute' => 'compare_price',
                        'value' => function($model) {
                            return $model->compare_price ? Yii::$app->formatter->asCurrency($model->compare_price) : null;
                        }
                    ],
                    'inventory_quantity',
                    'weight',
                    'status:boolean',
                    'featured:boolean',
                    'meta_title',
                    'meta_description',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
        <?php if (!Yii::$app->user->isGuest): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Purchase</h5>
                    <p class="card-text">
                        <strong>Price: </strong><?= Yii::$app->formatter->asCurrency($model->price) ?>
                    </p>
                    <p class="card-text">
                        <strong>Stock: </strong><?= $model->inventory_quantity ?> units
                    </p>
                    <?php if ($model->inventory_quantity > 0): ?>
                        <?= Html::a('Add to Cart', ['cart/add', 'id' => $model->id], [
                            'class' => 'btn btn-success btn-block',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]) ?>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-block" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
