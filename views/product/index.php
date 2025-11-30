<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('Filter', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin'): ?>
                <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <?php foreach ($dataProvider->getModels() as $product):
            $img = $product->getPrimaryImageUrl();
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-card h-100">
                    <?= Html::a(Html::img($img, ['alt' => $product->name, 'class' => 'img-fluid']), ['view', 'id' => $product->id]) ?>
                    <div class="p-2">
                        <?= Html::a(Html::encode($product->name), ['view', 'id' => $product->id], ['class' => 'product-name d-block text-truncate']) ?>
                        <div class="product-price mt-1"><?= Yii::$app->formatter->asCurrency($product->price) ?></div>
                        <div class="product-actions mt-2">
                            <?= Html::a('View', ['view', 'id' => $product->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            <?= Html::beginForm(['cart/add', 'id' => $product->id], 'post', ['class' => 'd-inline ms-2']) ?>
                                <button type="submit" class="btn btn-sm btn-primary">Add to cart</button>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex justify-content-center">
        <?= \yii\widgets\LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
    </div>
</div>
