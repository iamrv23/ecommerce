<?php
/* @var $this yii\web\View */
/* @var $model app\models\PermissionForm */
/* @var $perm \yii\rbac\Permission */

$this->title = 'Update Permission: ' . $perm->name;
?>
<div class="rbac-update-permission">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
    <p><strong>Name:</strong> <?= \yii\helpers\Html::encode($perm->name) ?></p>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
