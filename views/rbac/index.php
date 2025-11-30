<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $rolesData array */

$this->title = 'RBAC Roles';
?>
<div class="rbac-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>List of roles and their permissions.</p>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Role</th>
            <th>Description</th>
            <th>Permissions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rolesData as $roleName => $data): ?>
            <tr>
                <td><?= Html::encode($roleName) ?></td>
                <td><?= Html::encode($data['role']->description) ?></td>
                <td>
                    <?php if (!empty($data['permissions'])): ?>
                        <ul>
                            <?php foreach ($data['permissions'] as $p): ?>
                                <li><?= Html::encode($p->name) ?> - <?= Html::encode($p->description) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <em>No permissions</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
