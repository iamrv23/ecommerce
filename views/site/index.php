<?php
/* @var $this yii\web\View */
/* @var $products app\models\Product[] */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->name;
?>

<div class="hero text-center">
    <div class="container">
        <h1 class="display-5">Welcome to <?= Html::encode(Yii::$app->name) ?></h1>
        <p class="lead">Quality products. Great prices. Fast delivery.</p>
        <p>
            <?= Html::a('Shop Now', ['/product/index'], ['class' => 'btn btn-lg btn-cta']) ?>
            <?= Html::a('View Deals', ['/product/index', 'sort' => '-featured'], ['class' => 'btn btn-lg btn-outline-light ms-2']) ?>
        </p>
    </div>
</div>

<main class="container">
    <div class="row mt-4">
        <div class="col-12">
            <div class="promo-strip text-center">
                <strong>Free shipping</strong> on orders over $50 — <em>Limited time only</em>
            </div>
        </div>
    </div>

    <h2 class="section-heading">Featured Products</h2>
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <?php $img = $product->getPrimaryImageUrl(); ?>
                        <?= Html::a(Html::img($img, ['alt' => $product->name]), Url::to(['/product/view', 'id' => $product->id])) ?>
                        <div class="px-2">
                            <?= Html::a(Html::encode($product->name), Url::to(['/product/view', 'id' => $product->id]), ['class' => 'product-name']) ?>
                            <div class="product-price"><?= Html::encode($product->getFormattedPrice()) ?></div>
                            <div class="product-actions">
                                <?= Html::beginForm(['/cart/add', 'id' => $product->id], 'post', ['class' => 'd-inline']) ?>
                                    <button type="submit" class="btn btn-sm btn-primary">Add to cart</button>
                                <?= Html::endForm() ?>
                                <?= Html::a('View', ['/product/view', 'id' => $product->id], ['class' => 'btn btn-sm btn-outline-secondary ms-2']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No featured products available right now.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <h3>Why shop with us?</h3>
            <ul>
                <li>Curated catalog of quality products</li>
                <li>Secure payments and fast shipping</li>
                <li>Responsive customer support</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h3>Newsletter</h3>
            <p>Sign up for exclusive deals and updates.</p>
            <?= Html::beginForm(['/site/contact'], 'post') ?>
            <div class="input-group">
                <input type="email" name="email" class="form-control" placeholder="Your email address">
                <button class="btn btn-primary" type="submit">Subscribe</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

</main>
