<?php
/* @var $this yii\web\View */
/* @var $model app\models\RoleForm */

$this->title = 'Create Role';
?>
<div class="rbac-create-role">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
