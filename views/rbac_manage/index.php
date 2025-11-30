<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $roles array */
/* @var $perms array */

$this->title = 'Manage Roles & Permissions';
?>
<div class="rbac-manage-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <h3>Roles</h3>
            <p><?= Html::a('Create Role', ['create-role'], ['class' => 'btn btn-success']) ?></p>
            <table class="table table-striped">
                <thead><tr><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?= Html::encode($r->name) ?></td>
                        <td><?= Html::encode($r->description) ?></td>
                        <td>
                            <?= Html::a('Edit', ['update-role', 'name' => $r->name], ['class' => 'btn btn-sm btn-primary']) ?>
                            <?= Html::a('Permissions', ['edit-role-permissions', 'name' => $r->name], ['class' => 'btn btn-sm btn-info', 'style' => 'margin-left:6px;']) ?>
                            <?= Html::a('Delete', ['delete-role', 'name' => $r->name], ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Delete this role?'], 'style' => 'margin-left:6px;']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h3>Permissions</h3>
            <p><?= Html::a('Create Permission', ['create-permission'], ['class' => 'btn btn-success']) ?></p>
            <table class="table table-striped">
                <thead><tr><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($perms as $p): ?>
                    <tr>
                        <td><?= Html::encode($p->name) ?></td>
                        <td><?= Html::encode($p->description) ?></td>
                        <td>
                            <?= Html::a('Edit', ['update-permission', 'name' => $p->name], ['class' => 'btn btn-sm btn-primary']) ?>
                            <?= Html::a('Delete', ['delete-permission', 'name' => $p->name], ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Delete this permission?']]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
