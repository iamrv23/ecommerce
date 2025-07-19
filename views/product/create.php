<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Create Product';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'short_description')->textarea(['rows' => 3]) ?>
            <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            <?= $form->field($model, 'compare_price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            <?= $form->field($model, 'cost_price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            <?= $form->field($model, 'track_inventory')->checkbox() ?>
            <?= $form->field($model, 'inventory_quantity')->textInput(['type' => 'number']) ?>
            <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            <?= $form->field($model, 'status')->dropDownList([
                0 => 'Inactive',
                1 => 'Active'
            ]) ?>
            <?= $form->field($model, 'featured')->checkbox() ?>
            <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'meta_description')->textarea(['rows' => 3]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
