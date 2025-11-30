<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $role \yii\rbac\Role */
/* @var $allPerms array */
/* @var $current array */

$this->title = 'Manage Permissions for role: ' . $role->name;
?>
<div class="rbac-role-permissions">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Toggle permissions that should be assigned to this role.</p>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $items = [];
    foreach ($allPerms as $p) {
        $items[$p->name] = $p->description ?: $p->name;
    }
    ?>

    <div class="mb-3">
        <?= Html::checkboxList('permissions', $current, $items, ['itemOptions' => ['labelOptions' => ['style' => 'display:block;']]]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
