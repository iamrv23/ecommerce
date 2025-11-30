<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\ShoppingCart;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto'],
                    'items' => [
                    ['label' => 'Home', 'url' => ['/site/index']],
                    ['label' => 'Products', 'url' => ['/product/index']],
                    [
                        'label' => 'Admin',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Manage Products', 'url' => ['/product/index']],
                            ['label' => 'Manage Orders', 'url' => ['/order/index']],
                            ['label' => 'Manage Users', 'url' => ['/user/index']],
                            ['label' => 'RBAC', 'url' => ['/rbac/index']],
                            ['label' => 'RBAC Manage', 'url' => ['/rbac-manage/index']],
                        ],
                        'visible' => !Yii::$app->user->isGuest && Yii::$app->user->can('admin')
                    ],
                ],
    ]);
    
    $rightItems = [];
    // cart link: compute count from DB (user_id for logged-in, session_id for guests)
    $cartCount = 0;
    try {
        if (!Yii::$app->user->isGuest) {
            // sum quantities for logged-in user
            $sum = ShoppingCart::find()->where(['user_id' => Yii::$app->user->id])->sum('quantity');
            $cartCount = $sum !== null ? (int)$sum : 0;
        } else {
            $sessionId = session_id();
            if ($sessionId) {
                $sum = ShoppingCart::find()->where(['session_id' => $sessionId])->sum('quantity');
                $cartCount = $sum !== null ? (int)$sum : 0;
            }
        }
    } catch (\Throwable $e) {
        $cartCount = 0;
    }
    $rightItems[] = [
        'label' => '<i class="fas fa-shopping-cart"></i> Cart (' . $cartCount . ')',
        'url' => ['/cart/index'],
        'encode' => false,
    ];

    if (Yii::$app->user->isGuest) {
        $rightItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $rightItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $rightItems[] = ['label' => 'Profile', 'url' => ['/user/view', 'id' => Yii::$app->user->id]];
        $rightItems[] = '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $rightItems,
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
