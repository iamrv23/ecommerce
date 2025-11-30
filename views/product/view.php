<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3"><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-end">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin'): ?>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary me-2']) ?>
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

    <div class="row">
        <div class="col-md-6">
            <?php $img = $model->getPrimaryImageUrl(); ?>
            <div class="mb-3">
                <?= Html::img($img, ['class' => 'img-fluid rounded', 'alt' => $model->name]) ?>
            </div>
            <?php if ($model->images && count($model->images) > 1): ?>
                <div class="d-flex gap-2">
                    <?php foreach ($model->images as $imgModel):
                        $thumb = method_exists($imgModel, 'getThumbnailUrl') ? $imgModel->getThumbnailUrl() : ($imgModel->image_url ?? '/images/no-image.png');
                        ?>
                        <?= Html::a(Html::img($thumb, ['class' => 'img-thumbnail', 'style' => 'width:80px; height:80px; object-fit:cover;']), ['view', 'id' => $model->id]) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h4 class="mb-3"><?= Yii::$app->formatter->asCurrency($model->price) ?></h4>
            <p class="text-muted">SKU: <?= Html::encode($model->sku) ?></p>
            <p><?= nl2br(Html::encode($model->short_description ?: $model->description)) ?></p>

            <div class="mt-4">
                <div class="mb-2"><strong>Availability:</strong> <?= $model->inventory_quantity > 0 ? 'In stock' : 'Out of stock' ?></div>
                <?php if ($model->inventory_quantity > 0): ?>
                    <?= Html::beginForm(['/cart/add', 'id' => $model->id], 'post') ?>
                    <div class="input-group mb-3" style="max-width:180px;">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $model->inventory_quantity ?>" class="form-control">
                        <button class="btn btn-success" type="submit">Add to Cart</button>
                    </div>
                    <?= Html::endForm() ?>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>

            <hr>
            <p><strong>Details</strong></p>
            <ul class="list-unstyled">
                <li><strong>Weight:</strong> <?= $model->weight ? $model->weight . ' kg' : '—' ?></li>
                <li><strong>Categories:</strong> <?= implode(', ', array_map(function($c){return $c->name;}, $model->categories ?: [])) ?></li>
            </ul>
        </div>
    </div>
</div>
