<?php
/* @var $this yii\web\View */
/* @var $model app\models\RoleForm */
/* @var $role \yii\rbac\Role */

$this->title = 'Update Role: ' . $role->name;
?>
<div class="rbac-update-role">
    <h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
    <p><strong>Name:</strong> <?= \yii\helpers\Html::encode($role->name) ?></p>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
