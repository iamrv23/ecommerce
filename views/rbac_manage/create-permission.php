<?php
/* @var $this yii\web\View */
/* @var $model app\models\PermissionForm */

$this->title = 'Create Permission';
?>
<div class="rbac-create-permission">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
